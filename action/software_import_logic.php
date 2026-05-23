<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
include("../config/database.php");

// FIXED: Standardize redirect paths to prevent broken links (e.g., "oftware_import.php" or double extensions)
$redirect_url = "../admin_module/software_import.php";

// =========================================================================
// ROUTE A: DIRECT MANUAL PROFILE ENTRY PROCESSING
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute_manual_entry'])) {
    
    // Extract and sanitize text inputs to maintain database string uniformity
    $s_name    = isset($_POST['software_name']) ? trim($_POST['software_name']) : '';
    $s_dev     = isset($_POST['developer'])     ? trim($_POST['developer'])     : '';
    $s_ver     = isset($_POST['version'])       ? trim($_POST['version'])       : '';
    $s_cat     = isset($_POST['category'])      ? trim($_POST['category'])      : 'General';
    $s_license = isset($_POST['license_type'])  ? trim($_POST['license_type'])  : 'N/A';
    $target_lab = isset($_POST['target_lab'])   ? trim($_POST['target_lab'])   : 'all';

    // Verify minimum viable datasets are Present before proceeding
    if (empty($s_name) || empty($s_dev) || empty($s_ver)) {
        header("Location: {$redirect_url}?status=empty");
        exit();
    }

    $insert_query = "INSERT INTO software_applications (software_name, developer, version, category, license_type, target_lab) VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($insert_query)) {
        $stmt->bind_param("ssssss", $s_name, $s_dev, $s_ver, $s_cat, $s_license, $target_lab);
        
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: {$redirect_url}?status=success");
        } else {
            $stmt->close();
            header("Location: {$redirect_url}?status=error");
        }
        exit();
    } else {
        header("Location: {$redirect_url}?status=error");
        exit();
    }
}

// =========================================================================
// ROUTE B: EXISTING BATCH CSV IMPORT STREAM PROCESSING
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute_import'])) {
    
    $target_lab = isset($_POST['target_lab']) ? trim($_POST['target_lab']) : 'all';

    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName    = $_FILES['csv_file']['name'];
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension !== 'csv') {
            header("Location: {$redirect_url}?status=error");
            exit();
        }

        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            
            $headers = fgetcsv($handle, 1000, ",");
            
            if (!$headers) {
                fclose($handle);
                header("Location: {$redirect_url}?status=empty");
                exit();
            }

            $headers = array_map('strtolower', array_map('trim', $headers));

            $idx_name    = array_search('software name', $headers);
            $idx_dev     = array_search('developer', $headers);
            $idx_ver     = array_search('version', $headers);
            $idx_cat     = array_search('category', $headers);
            $idx_license = array_search('license type', $headers);

            if ($idx_name === FALSE || $idx_dev === FALSE || $idx_ver === FALSE) {
                fclose($handle);
                header("Location: {$redirect_url}?status=error");
                exit();
            }

            $insert_query = "INSERT INTO software_applications (software_name, developer, version, category, license_type, target_lab) VALUES (?, ?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($insert_query)) {
                
                $rows_imported = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    
                    if (empty(array_filter($data))) continue;

                    $s_name    = isset($data[$idx_name]) ? trim($data[$idx_name]) : '';
                    $s_dev     = isset($data[$idx_dev]) ? trim($data[$idx_dev]) : '';
                    $s_ver     = isset($data[$idx_ver]) ? trim($data[$idx_ver]) : '';
                    $s_cat     = ($idx_cat !== FALSE && isset($data[$idx_cat])) ? trim($data[$idx_cat]) : 'General';
                    $s_license = ($idx_license !== FALSE && isset($data[$idx_license])) ? trim($data[$idx_license]) : 'N/A';

                    if (!empty($s_name)) {
                        $stmt->bind_param("ssssss", $s_name, $s_dev, $s_ver, $s_cat, $s_license, $target_lab);
                        $stmt->execute();
                        $rows_imported++;
                    }
                }
                
                $stmt->close();
                fclose($handle);

                if ($rows_imported > 0) {
                    header("Location: {$redirect_url}?status=success");
                } else {
                    header("Location: {$redirect_url}?status=empty");
                }
                exit();

            } else {
                fclose($handle);
                header("Location: {$redirect_url}?status=error");
                exit();
            }

        } else {
            header("Location: {$redirect_url}?status=error");
            exit();
        }

    } else {
        header("Location: {$redirect_url}?status=empty");
        exit();
    }
}

// Catch-all fallthrough for unauthorized or direct URL browsing requests
header("Location: {$redirect_url}");
exit();