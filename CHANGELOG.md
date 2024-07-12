# Changelog

## v1.0.6 - 2024-07-12

**Added:** Backup date meta field
**Updated:** Cron job for auth token status

## v1.0.5 - 2024-07-12

**Updated:** Fixes in cron job `wpc2_gdoc_register_run_gdrive_backup_job` about post status and pagination

## v1.0.4 - 2024-07-12

**Updated:** Cron job `wpc2_gdoc_register_run_gdrive_backup_job` to execute post backup

## v1.0.3 - 2024-07-11

- **Added:** Cron job to refresh auth token
- **Added:** Cron job to execute backup daily

## v1.0.2 - 2024-07-11

- **Updated:** Method `create_backup` Post id is now part of the arguments array.
- **Added:** Method `create_file` The create a new backup file
- **Added:** Method `update_file` The update existing backup file
- **Added:** New filter `cbox_wpc2_gdoc_backup_post_types` to manipulate the allowed post types

## v1.0.1 - 2024-07-10

- **Added:** Method `user_can_see_backup_column` to check if the current logged user can see the backup column
- **Added:** New filter `cbox_wpc2_gdoc_backup_file_name` to manipulate the backup file name before get saved
- **Added:** New filter `cbox_wpc2_gdoc_backup_file_content` to manipulate the backup content before get saved
- **Added:** New filter `cbox_wpc2_gdoc_backup_file_type` to manipulate the backup file mime type
- **Updated:** Method `create_backup` now accepts an array of arguments

## v1.0.0 - 2024-07-09

Initial Release
