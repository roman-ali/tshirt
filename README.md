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
and you can also run the following if you wish to run the tests
```
php artisan test
```

5. You can access the website from [http://localhost](http://localhost)

## Usage
This is a RESTful API. Authentication has not been implemented for this demo.



The properties for companies are:
id, name, slug, created_at, updated_at

The properties for contacts are:
id, company_id, name, phone_number, created_at, updated_at

The properties for notes are:
id, contact_id, note, created_at, updated_at



1. To create a company, send a POST request to http://localhost/api/companies with two parameters, 'name' and 'slug'
 - Note: bulk creation has not been implemented as it has tradeoffs in terms of needing to do database transaction, and presenting errors back to the client
2. To fetch a company, send a GET request to http://localhost/api/companies/{companyId}
3. To fetch all companies (paginated), send a GET request to http://localhost/api/companies. You can add a 'page' parameter with an integer value to load a specific page.
4. To update a company, send a PATCH request to http://localhost/api/companies/{companyId} with the name parameter.
 ​- Note: PATCH requests were preferred over PUT for update operations, because PUT expects all model properties as parameters, and for the purposes of this demo it was more convenient to not have to include the timestamps, or in the case of companies the slug property, since it makes more sense to calculate the slug from the name, thus helping to maintain consistency and predictability between name and slug.
5. To delete a company, send a DELETE request to http://localhost/api/companies/{companyId}


Contacts and Notes have similar API endpoints as above, except that 'companies' will be replaced with 'contacts', and for notes it will be replaced with 'notes'.


6. To fetch a paginated collection of all contacts at a company, send a GET request to http://localhost/api/contacts/company/{companyId}

7. To fetch a paginated collection of all contacts matching a name or company name, send a GET request to http://localhost/api/contacts with parameter 'name' or 'company_name'

8. To fetch a paginated collection of all notes for a contact, send a GET request to http://localhost/api/notes/contact/{contactId}
