<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/24
 * Time: 下午9:24
 */

namespace App\Services\Gateway;

use Exception;
use Omnipay\Omnipay;
use App\Services\View;
use App\Services\Auth;
use App\Models\Paylist;
use App\Models\Setting;

class AopF2F extends AbstractPayment
{
    public static function _name() 
    {
        return 'f2fpay';
    }

    public static function _enable() 
    {
        return self::getActiveGateway('f2fpay');
    }

    public static function _readableName() {
        return "支付宝在线充值";
    }

    private function createGateway()
    {
        $configs = Setting::getClass('f2f');
        $gateway = Omnipay::create('Alipay_AopF2F');
        $gateway->setSignType('RSA2'); //RSA/RSA2
        $gateway->setAppId($configs['f2f_pay_app_id']);
        $gateway->setPrivateKey($configs['f2f_pay_private_key']); // 可以是路径，也可以是密钥内容
        $gateway->setAlipayPublicKey($configs['f2f_pay_public_key']); // 可以是路径，也可以是密钥内容
        if ($configs['f2f_pay_notify_url'] == '') {
            $notifyUrl = self::getCallbackUrl();
        } else {
            $notifyUrl = $configs['f2f_pay_notify_url'];
        }
        $gateway->setNotifyUrl($notifyUrl);
        return $gateway;
    }


    public function purchase($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        $user = Auth::getUser();
        if ($amount == '') {
            return $response->withJson([
                'ret' => 0,
                'msg' => '订单金额错误：' . $amount
            ]);
        }

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->tradeno = self::generateGuid();
        $pl->total = $amount;
        $pl->save();

        $gateway = $this->createGateway();

        $request = $gateway->purchase();
        $request->setBizContent([
            'subject' => $pl->tradeno,
            'out_trade_no' => $pl->tradeno,
            'total_amount' => $pl->total
        ]);

        /** @var \Omnipay\Alipay\Responses\AopTradePreCreateResponse $response */
        $aliResponse = $request->send();

        // 获取收款二维码内容
        $qrCodeContent = $aliResponse->getQrCode();

        $return['ret'] = 1;
        $return['qrcode'] = $qrCodeContent;
        $return['amount'] = $pl->total;
        $return['pid'] = $pl->tradeno;

        return json_encode($return);
    }

    public function notify($request, $response, $args)
    {
        $gateway = $this->createGateway();
        $aliRequest = $gateway->completePurchase();
        $aliRequest->setParams($_POST);

        try {
            /** @var \Omnipay\Alipay\Responses\AopCompletePurchaseResponse $response */
            $aliResponse = $aliRequest->send();
            $pid = $aliResponse->data('out_trade_no');
            if ($aliResponse->isPaid()) {
                $this->postPayment($pid, '支付宝当面付 ' . $pid);
                die('success'); //The response should be 'success' only
            }
        } catch (Exception $e) {
            die('fail');
        }
    }


    public static function getPurchaseHTML()
    {
        return View::getSmarty()->fetch('user/aopf2f.tpl');
    }

    public function getReturnHTML($request, $response, $args)
    {
        return 0;
    }

    public function getStatus($request, $response, $args)
    {
        $p = Paylist::where('tradeno', $_POST['pid'])->first();
        $return['ret'] = 1;
        $return['result'] = $p->status;
        return json_encode($return);
    }
}
