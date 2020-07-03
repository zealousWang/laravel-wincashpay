<?php

namespace Wincash\Payment\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wincashpay:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wincashpay instalation wizard';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $env = '';
        $path = base_path('.env');
        $ipn_secret = Str::random(20);
        $email = '';
        $marchatid = '';


        $this->line(PHP_EOL);
        $this->line('---------------------------------------------------------');
        $this->line('         Wellcome to the wincashpay installation');
        $this->line('---------------------------------------------------------');
        $public_key = $this->ask('insert your public key ?');
        $secret_key = $this->ask('insert your secret key ?');

        /*$this->line('for example: IDR, USD, CAD, EUR, ARS, AUD, AZN, BGN, BRL, BYN, CHF, CLP, CNY, COP, CZK');
        $currency = $this->ask('insert default currency ?');*/

        $env  .=  'WINCASHPAY_PUBLIC_KEY=' . $public_key . PHP_EOL
                . 'WINCASHPAY_PRIVATE_KEY=' . $secret_key . PHP_EOL;


        $ipnconfirm = $this->confirm('Do you want to enable IPN mode ?');

        if($ipnconfirm) {
            $marchatid = $this->ask('insert your merchant ID ?');
            $email = $this->ask('insert your debuging email address ?');

            $env  .=  'WINCASHPAY_IPN_ACTIVATE=true'
                    . 'WINCASHPAY_MERCHANT_ID=' . $marchatid . PHP_EOL
                    . 'WINCASHPAY_IPN_SECRET=' . $ipn_secret . PHP_EOL
                    . 'WINCASH_IPN_DEBUG_EMAIL=' . $email;
        }
            
        $this->line($env);
        if($this->confirm('your data is correct ?')){
            if (file_exists($path)) {
                $file = file_get_contents($path);
                file_put_contents($path, $file . PHP_EOL . $env);
            }
            $this->line(PHP_EOL);
            $this->line('---------------------------------------------------------');
            $this->info('Visit this link https://wincashpay.com/acct-settings and got to the Merchant Settings tab And insert data below !');
            $this->line(PHP_EOL);
            $this->line('IPN Secret         : ' . $ipn_secret);
            $this->line('IPN URL            : ' . url('/wincashpay/ipn'));
            $this->line('Status / Log Email : ' . $email);
            $this->line(PHP_EOL);
            $this->call('migrate');
            $this->info('installation Finish');
            $this->line('---------------------------------------------------------');
          }else{
            $this->error('installation canceled!');
          }
    }
}
