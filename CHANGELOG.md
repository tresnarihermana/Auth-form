# Change Logs

## [Roles and Permission] - 03-08-2025

## *Depedency dan semacamnya*
+ Spatie/Laravel-permission  
+ Spatie/laravel-activitylog  
+ log-viewer  
+ primeicons  

## *Authentication & security Update*
+ change Password Rule, now password can have space  
+ fix flow register new user, sekarang jadi register->verify-email->Logout (oleh sistem)-> login ulang  
+ Change Swal2 message in login page  
+ new user now have user role when register  
+ new middleware EnsureUserIsActive  
+ Middleware for role and permission (Spatie)  
+ can() untuk cek izin  

## *Roles and Permission + User Managements*
+ add new User Management pages (Complete with Show-Create-Edit-Delete feature)  
+ add Swal2 message to User managements Page  
+ add new Roles Managements Page (Complete with Create-Edit-Delete Feature)  
+ add Swal2 message to Roles Management Page  
+ add new Permissions Management Page (Complete with Create-Edit-Delete Feature)  
+ add Swal2 message  
+ now user have Active and Disabled status  
+ disabled users cant login till the acount is activated by admin  

## *Logs Page (still on process)*
+ add logs page  
+ add activity logs when admin Create-delete-edit user  
+ add log-viewer  

## *Database and seeders*
+ add roles, role_has_permissions, permissions, activity_logs to migration  
+ Seeder 100 user + Role and permission to database  

## *UI/UX improvements*
+ Table User Managements mendukung avatar  
+ Pagination and Filter untuk user, role dan permissions  
+ label permission di pangkas biar ga kepanjangan  
+ Tambah PrimeIcons  
+ link di sidebar diperbarui  
