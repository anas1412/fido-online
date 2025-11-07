# Setting up the Cron Job for Invites Cleanup

This guide explains how to set up a cron job on your server to automatically run the `invites:cleanup` Artisan command for your Laravel project. This command deletes expired and unused tenant invite codes from your database.

## Prerequisites

*   SSH access to your server.
*   Your Laravel project deployed on the server.
*   Basic understanding of cron jobs.

## Steps

1.  **Connect to Your Server via SSH**

    Open your terminal and connect to your server using SSH:

    ```bash
    ssh username@your_server_ip_or_domain
    ```

    Replace `username` with your server username and `your_server_ip_or_domain` with your server's IP address or domain name.

2.  **Navigate to Your Project Directory**

    Once connected, navigate to the root directory of your Laravel project. This is typically where your `artisan` file is located.

    ```bash
    cd /path/to/your/laravel/project
    ```

    Replace `/path/to/your/laravel/project` with the actual path to your project.

3.  **Open the Crontab Editor**

    To add a new cron job, open the crontab editor:

    ```bash
    crontab -e
    ```

    This will open a text editor (usually `vi` or `nano`) where you can add cron job entries.

4.  **Add the Cron Job Entry**

    Add the following line to the end of the file. This line tells the server to run the Laravel scheduler every minute.

    ```cron
    * * * * * cd /path/to/your/laravel/project && php artisan schedule:run >> /dev/null 2>&1
    ```

    **Explanation of the cron entry:**

    *   `* * * * *`: This part specifies the schedule. `*` for every minute, hour, day of month, month, and day of week. This means the command will run every minute.
    *   `cd /path/to/your/laravel/project`: Changes the current directory to your Laravel project's root.
    *   `php artisan schedule:run`: This is the Laravel command that runs all scheduled tasks defined in your `AppServiceProvider.php` (or `Kernel.php` in older Laravel versions).
    *   `>> /dev/null 2>&1`: This redirects all output (standard output and standard error) to `/dev/null`, preventing cron from sending you emails with command output every minute. You can remove this part if you want to receive emails about cron job output (e.g., for debugging).

    **Important:** Remember to replace `/path/to/your/laravel/project` with the actual absolute path to your project on the server.

5.  **Save and Exit the Crontab Editor**

    *   If using `nano`: Press `Ctrl+X`, then `Y` to confirm saving, then `Enter`.
    *   If using `vi`: Press `Esc`, then type `:wq` and press `Enter`.

6.  **Verify the Cron Job (Optional)**

    You can list your active cron jobs to ensure your entry was added correctly:

    ```bash
    crontab -l
    ```

## How it Works

Your `AppServiceProvider.php` defines the `invites:cleanup` command to run `daily()`:

```php
// app/Providers/AppServiceProvider.php
public function boot(Schedule $schedule): void
{
    $schedule->command('invites:cleanup')->daily();
}
```

The cron job you set up (`* * * * * php artisan schedule:run`) tells your server to execute `php artisan schedule:run` every minute. Laravel's scheduler then checks all registered scheduled commands and only runs those that are due (e.g., `invites:cleanup` will only run once every 24 hours when `schedule:run` is executed).