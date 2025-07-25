<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_thank_you_email($to_email, $callsign) {
    $mail = new PHPMailer(true);

    try {
        // SMTP服务器配置（根据你邮箱服务修改）
        $mail->isSMTP();
                         $mail->Host = ''; //邮件服务器地址
                        $mail->SMTPAuth = true;
                        $mail->Username = '';//邮箱用户名
                        $mail->Password = ''; // 请替换为你的密码
                        $mail->CharSet = 'UTF-8';
                        $mail->Encoding = 'base64';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('发件邮箱', '你的呼号');
                        $mail->addAddress($to_email);
        // 邮件内容
        $mail->isHTML(true);
        $mail->Subject = '感谢您的 QSL 卡片';
        $mail->Body    = "尊敬的业余无线电爱好者，<br><br>"
            . "我们已收到您的 QSL 卡片，您的呼号是：<b>" . htmlspecialchars($callsign) . "</b>。<br>"
            . "非常高兴与您通联！<br><br>"
            . "祝好<br>";

        $mail->AltBody = "我已收到您的 QSL 卡片，呼号是：" . $callsign . "。非常感谢您的支持！";

        $mail->send();
        // 发送成功不返回内容
        return true;
    } catch (Exception $e) {
        error_log("邮件发送失败: {$mail->ErrorInfo}");
        return false;
    }
}
