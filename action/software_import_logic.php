<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
include("../config/database.php");

// 1. Double check that the request came via form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute_import'])) {
    
    // 2. Extract selected Target Lab metadata profile
    $target_lab = isset($_POST['target_lab']) ? trim($_POST['target_lab']) : 'all';

    // 3. Confirm file array elements exist and possess zero upload payload faults
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName    = $_FILES['csv_file']['name'];
        
        // Verify extension safety parameters just in case
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension !== 'csv') {
            header("Location: ../admin/import_page.php?status=error");
            exit();
        }

        // 4. Parse the CSV file handle securely
        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            
            // Read header row to map out programmatic layouts safely
            $headers = fgetcsv($handle, 1000, ",");
            
            // If the template is structurally empty, break early
            if (!$headers) {
                fclose($handle);
                header("Location: ../admin/import_page.php?status=empty");
                exit();
            }

            // Standardize headers (trim whitespace and lower case to combat discrepancies)
            $headers = array_map('strtolower', array_map('trim', $headers));

            // Map expected indexes based on column names from template string
            // Template columns: "Software Name", "Developer", "Version", "Category", "License Type"
            $idx_name    = array_search('software name', $headers);
            $idx_dev     = array_search('developer', $headers);
            $idx_ver     = array_search('version', $headers);
            $idx_cat     = array_search('category', $headers);
            $idx_license = array_search('license type', $headers);

            // If template headers do not correlate correctly, trigger structural system failure
            if ($idx_name === FALSE || $idx_dev === FALSE || $idx_ver === FALSE) {
                fclose($handle);
                header("Location: ../admin/import_page.php?status=error");
                exit();
            }

            // 5. Build parameterized query loop to prevent SQL injections
            $insert_query = "INSERT INTO software_applications (software_name, developer, version, category, license_type, target_lab) VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($insert_query)) {
                
                $rows_imported = 0;

                // Loop line-by-line through file content profiles
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    
                    // Skip empty rows
                    if (empty(array_filter($data))) continue;

                    // Fallbacks applied to prevent out-of-bounds offset notices
                    $s_name    = isset($data[$idx_name]) ? trim($data[$idx_name]) : '';
                    $s_dev     = isset($data[$idx_dev]) ? trim($data[$idx_dev]) : '';
                    $s_ver     = isset($data[$idx_ver]) ? trim($data[$idx_ver]) : '';
                    $s_cat     = ($idx_cat !== FALSE && isset($data[$idx_cat])) ? trim($data[$idx_cat]) : 'General';
                    $s_license = ($idx_license !== FALSE && isset($data[$idx_license])) ? trim($data[$idx_license]) : 'N/A';

                    // Ensure minimum viable field blocks exist before pushing down loop lines
                    if (!empty($s_name)) {
                        $stmt->bind_param("ssssss", $s_name, $s_dev, $s_ver, $s_cat, $s_license, $target_lab);
                        $stmt->execute();
                        $rows_imported++;
                    }
                }
                
                $stmt->close();
                fclose($handle);

                // Determine redirect path state parameter flag based on records created
                if ($rows_imported > 0) {
                    header("Location: ../admin_module/software_import.php?status=success");
                } else {
                    header("Location: ../admin_module/oftware_import.php?status=empty");
                }
                exit();

            } else {
                // Statement construction engine broke down
                fclose($handle);
                header("Location: ../admin_module/software_import.php?status=error");
                exit();
            }

        } else {
            // File streaming wrapper stream error
            header("Location: ../admin_module/software_import.php?status=error");
            exit();
        }

    } else {
        // Core file asset upload mechanism failure code returned
        header("Location: ../admin_module/software_import.php?status=empty");
        exit();
    }
} else {
    // Direct unauthorized link browsing bypass detection fallback
    header("Location: ../admin_module/software_import.php.php");
    exit();
}