ALTER TABLE `#__it_progress`
 DROP FOREIGN KEY `idx_progress_issue_id_fk`;
ALTER TABLE `#__it_progress`
 ADD CONSTRAINT `idx_progress_issue_id_fk` FOREIGN KEY (issue_id) REFERENCES `#__it_issues` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE;
