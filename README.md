# SSH Port Forwarding Script

This PHP script allows for managing SSH port forwarding for remote server connections.

## Setup Instructions

### Requirements:
* PHP 8.0 or higher installed on the local machine.
* Access to the remote server for SSH port forwarding.<br><br>


**Usage**:
    - Run the script to start the SSH port forwarding application.
    - The script will manage SSH tunnels for the defined port forwarding configurations.
<br><br>
    
## Install PHP (8.x) in Ubuntu : 
```cli
sudo dpkg -l | grep php | tee packages.txt
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-{bz2,curl,mbstring,intl,sqlite3}
```

## How to Use



1 . **Configuration**:
    - Update the following constants in the script according to your environment:<br>
        - `sshPort`: The SSH port to connect to on the remote server.<br>
        - `sshHost`: The IP address or domain of the remote server .<br>
        - `forwardingPorts`: An array of local and remote port pairs for port forwarding.

```php
class RemoteManager
{
    public const sshPort = 22; 
    public const sshHost = 'xxx.xxx.xxx.xxx'; 
    public const forwardingPorts = ['80:80','443:443']; 
}
```


<br><br>
**Running the Script**:
```bash
php start.php
```


<br><br>
## Running the Script via Crontab

To schedule the SSH port forwarding script to run every 5 minutes using the crontab tool, follow these steps:

1. **Edit Crontab**:
    Open your terminal and edit the crontab file by running:
    ```bash
    crontab -e
    ```

2. **Add Crontab Entry**:
    Add the following line at the end of the crontab file to run the script every 5 minutes:
    ```bash
    */5 * * * * /usr/bin/php /path/to/SSH_Port_Forwarder.php >> /path/to/log_file.log 2>&1
    ```

