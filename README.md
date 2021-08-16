# tshirt

tshirt is a demo Laravel application with api endpoints for setting and getting companies, contacts and notes, where each company can have many contacts, and each contact can have many notes.

## Installation (extra steps for Windows 10)

You may need to install Windows Terminal and run the following commands on it as administrator
```
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
```
```
dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
```
(then restart computer and relaunch Windows Terminal as admnistrator)
```
wsl --set-default-version 2
```

Install and launch Ubuntu from the Micriosoft Store, then returning to the Windows Terminal, check the wsl version by executing the following command:
```
wsl --list --verbose
```
(This should show Ubuntu version 2 running)

## Installation
1. Download, install and run the correct version of Docker Desktop for your operating system from [docker.com](https://www.docker.com/products/docker-desktop).
 ​- If on Windows, ensure the following options are checked during the installation process
 ​​- "Enable Hyper-V Windows Features"
 ​​- "Install required Windows components for WSL-2"
 
2. If on Windows, double check Docker settings to ensure
 ​- "Use the WSL 2 based engine" option is checked, in Settings > General.
 ​- "Enable integration with my default WSL distro" is checked, in Settings > Resources > WSL Integration
 ​- Ensure Ubuntu is enabled launch
 ​- Click on "Apply and Restart" if you had to change any of the settings

3. Having cloned this repository onto your local machine, navigate to it in the terminal and execute 
```
./vendor/bin/sail up -d
```

4. Open the Docker Desktop dashboard and click on the "CLI" button which appears when hovering over the green (running)tshirt_laravel.test_1 container. This will give access to the container's command line interface rather than your local machine's CLI.

From within the container's CLI execute 
```
php artisan key:generate
```
```
composer update
```
```
php artisan migrate
```
and you cxan also run the following if you wish to run the tests
```
php artisan test
```


5. You can access the website from [http://localhost](http://localhost)