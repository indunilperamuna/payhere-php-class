<?php

/*
MIT License

Copyright (c) 2021 Indunil Peramuna

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Developed By : Indunil Peramuna MBCS(UK)
Organization : Siyalude Business Solutions
Email : indunil@siyalude.biz
Contact  : +94 777671771 | +94 114 363612
Contact Us for any custom Payhere Integration
 * */

class Payhere
{

    protected $merchantId;

    protected $merchantSecret;

    protected $liveUrl = 'https://www.payhere.lk/pay/checkout';

    protected $sandboxUrl = 'https://sandbox.payhere.lk/pay/checkout';

    protected $returnUrl;

    protected $cancelUrl;

    protected $notifyUrl;

    protected $live;

    protected $url;

    /**
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @param mixed $cancelUrl
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * @param mixed $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    /**
     * Payhere constructor.
     * @param boolean $live
     * @param string $merchantId
     * @param string $merchantSecret
     * @param string $returnUrl
     * @param string $cancelUrl
     * @param string $notifyUrl
     */
    public function __construct(boolean $live, string $merchantId, string $merchantSecret, string $returnUrl = '' , string $cancelUrl = '', string $notifyUrl = '')
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->returnUrl = $returnUrl;
        $this->cancelUrl = $cancelUrl;
        $this->notifyUrl = $notifyUrl;
        $this->url = $this->sandboxUrl;
        if($live){
            $this->url = $this->liveUrl;
        }
    }

    /**
     * Payhere Button
     * @param array $data
     */
    public function payButton(array $data) : string{

        $htmlContent = '<form method="post" action="'. $this->url . '">'
                       .'<input type="hidden" name="merchant_id" value="'. $this->merchantId . '">'
                       .'<input type="hidden" name="return_url" value="'. $this->returnUrl . '">'
                       .'<input type="hidden" name="cancel_url" value="'. $this->cancelUrl . '">'
                       .'<input type="hidden" name="notify_url" value="'. $this->notifyUrl . '">'
                       .'<input type="hidden" name="order_id" value="'. $data['orderId']. '">'
                       .'<input type="hidden" name="items" value="'. $data['items']. '"><br>'
                       .'<input type="hidden" name="currency" value="'. $data['currency']. '">'
                       .'<input type="hidden" name="amount" value="'. $data['amount']. '">'
                       .'<input type="hidden" name="first_name" value="'. $data['firstName']. '">'
                       .'<input type="hidden" name="last_name" value="'. $data['lastName']. '">'
                       .'<input type="hidden" name="email" value="'. $data['email']. '">'
                       .'<input type="hidden" name="phone" value="'. $data['phone']. '">'
                       .'<input type="hidden" name="address" value="'. $data['address']. '">'
                       .'<input type="hidden" name="city" value="'. $data['city']. '">'
                       .'<input type="hidden" name="country" value="'. $data['country']. '">'
                       .'<input type="hidden" name="hash" value="'. $this->getGenerateHash($this->merchantId, $data['orderId'], $data['amount'], $data['currency'], $this->merchantSecret). '">'
                       .'<input type="submit" class="payhereBtn" value="'. $data['btnText']. '">'
                       .'</form>';

        return $htmlContent;

    }

    private function getGenerateHash($merchantId, $orderId, $amount, $currency, $merchantSecret){
        return strtoupper (md5 ( $merchantId . $orderId . $amount . $currency . strtoupper(md5($merchantSecret)) ) );
    }

    /**
     * Payment Verification
     * @param array $paymentData
     */

    public function paymentVerification(array $paymentData): bool{

        $merchantid = $paymentData['merchant_id'];
        $orderId   = $paymentData['order_id'];
        $amount = $paymentData['payhere_amount'];
        $currency = $paymentData['payhere_currency'];
        $status_code = $paymentData['status_code'];
        $md5sig = $paymentData['md5sig'];

        $local_md5sig = $this->getGenerateHash($merchantid, $orderId, $amount, $currency, $this->merchantSecret);

        if (($local_md5sig === $md5sig) AND ($status_code == 2) ){
            return true;
        }

        return false;
    }


}