<?php

namespace App\Command;

use App\Services\Mail;
use App\Utils\Telegram;
use App\Models\Setting;
use Exception;
use RuntimeException;

class Backup extends Command
{
    public $description = ''
        . '├─=: php xcat Backup [选项]' . PHP_EOL
        . '│ ├─ full                    - 整体数据备份' . PHP_EOL
        . '│ ├─ simple                  - 只备份核心数据' . PHP_EOL;

    public function boot()
    {
        if (count($this->argv) === 2) {
            echo $this->description;
        } else {
            $methodName = $this->argv[2];
            if ($methodName == 'full') {
                $this->backup(true);
            } else {
                $this->backup(false);
            }
        }
    }

    public function backup($full = false)
    {
        $configs = Setting::getClass('backup');
        
        ini_set('memory_limit', '-1');
        $to = $configs['auto_backup_email'];
        if ($to == null) {
            return false;
        }
        if (!mkdir('/tmp/ssmodbackup/') && !is_dir('/tmp/ssmodbackup/')) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', '/tmp/ssmodbackup/'));
        }
        $db_address_array = explode(':', $_ENV['db_host']);
        if ($full) {
            system('mysqldump --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . $_ENV['db_database'] . ' > /tmp/ssmodbackup/mod.sql');
        } else {
            system(
                'mysqldump --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' ' . $_ENV['db_database'] . ' announcement blockip bought code coupon link login_ip payback shop user_invite_code node user_password_reset ticket unblockip user user_token email_verify detect_list paylist > /tmp/ssmodbackup/mod.sql',
                $ret
            );
            system(
                'mysqldump --opt --user=' . $_ENV['db_username'] . ' --password=' . $_ENV['db_password'] . ' --host=' . $db_address_array[0] . ' ' . (isset($db_address_array[1]) ? '-P ' . $db_address_array[1] : '') . ' -d ' . $_ENV['db_database'] . ' alive_ip node_info node_online_log detect_log telegram_session >> /tmp/ssmodbackup/mod.sql',
                $ret
            );
        }

        system('cp ' . BASE_PATH . '/config/.config.php /tmp/ssmodbackup/configbak.php', $ret);
        echo $ret;
        $backup_passwd = $configs["auto_backup_password"] == "" ? "" : " -P " . $configs["auto_backup_password"];
        system('zip -r /tmp/ssmodbackup.zip /tmp/ssmodbackup/* ' . $backup_passwd, $ret);
        $subject = $_ENV['appName'] . '-备份成功';
        $text = '您好，系统已经为您自动备份，请查看附件，用您设定的密码解压。';
        try {
            Mail::send($to, $subject, 'news/backup.tpl', [
                'text' => $text
            ], [
                '/tmp/ssmodbackup.zip'
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        system('rm -rf /tmp/ssmodbackup', $ret);
        system('rm /tmp/ssmodbackup.zip', $ret);

        if ($configs['auto_backup_notify'] == true) {
            Telegram::Send('备份工作已经完成');
        }
    }
}
