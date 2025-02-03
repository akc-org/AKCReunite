# AKCReunite
## Spinup Instructions
1. clone this repository
2. cd into the base directory and run docker-compose up -d
3. Import the database (you may have to import several times with different encodings and then restart the application for the data to show)
4. Change the siteurl to 'http://localhost' and the value for both home entries to the same value (there is another on line 36)
5. In the file structure for the WordPress container, edit the database name to be 'reunite', the password to be 'wordpress' and the table prefix to be 'wp_R3uN1t3_'.
6. Disable Yoast and Yoast Premium

## Premium Plugins
- Yoast Premium 