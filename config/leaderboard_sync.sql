-- Step 1: Add the New Columns to your Database Schema
ALTER TABLE sit_in_records 
ADD COLUMN task_status VARCHAR(50) DEFAULT 'Pending' AFTER language,
ADD COLUMN behavior_score INT DEFAULT NULL AFTER task_status,
ADD COLUMN points_earned_this_session DECIMAL(5, 2) DEFAULT 0.00 AFTER behavior_score;

ALTER TABLE students 
ADD COLUMN accumulated_points DECIMAL(7, 2) DEFAULT 0.00,
ADD COLUMN sessions_earned INT DEFAULT 0;


--Step 2: Backfill Points for Legacy History Logs
UPDATE sit_in_records 
SET 
    task_status = 'Completed',
    behavior_score = 10,
    points_earned_this_session = ROUND(
        LEAST(10, (COALESCE(TIMESTAMPDIFF(SECOND, login_time, logout_time), 3600) / 3600 / 3.0) * 10) * 0.20 + 6.0 + 2.0, 
        2
    )
WHERE 
    (points_earned_this_session IS NULL OR points_earned_this_session = 0);


-- Step 3: Aggregate Points and Sync the Leaderboard Standings
UPDATE students s
SET s.accumulated_points = COALESCE((
    SELECT SUM(points_earned_this_session) 
    FROM sit_in_records 
    WHERE student_pk_id = s.id
), 0);

--Step 4: Award Bonus Milestone Sessions Retroactively
UPDATE students 
SET 
    sessions_earned = FLOOR(accumulated_points / 50),
    sit_ins = sit_ins + FLOOR(accumulated_points / 50)
WHERE 
    accumulated_points >= 50;

-- query students
SELECT name, student_id, accumulated_points 
FROM students 
ORDER BY accumulated_points DESC 
LIMIT 10;