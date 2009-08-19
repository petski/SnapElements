<?php
class Config {
        protected $defaults = array(
                        'db.host'               => '',
                        'db.user'               => '',
                        'db.pass'               => '',
                        'db.name'               => '',
                        'db.type'               => '',

                        'iface.lang'            => 'en_EN',
                        'iface.style'           => 'default',
                        'iface.rowamount'       => '50',
                        'iface.expire'          => '1800',

                        'dns.hostmaster'        => '',
                        'dns.ns1'               => '',
                        'dns.ns2'               => '',
                        'dns.ttl'               => '86400',
                        'dns.fancy'             => false,
                        'dns.strict_tld_check'  => true,

			'jsonrpc.uri'		=> 'api/jsonrpc.php',

                        'application.version' 	=> '0.1',
                        'application.revision'	=> '$Id$',
                        'application.name'	=> 'Petje\'s PowerAdmin',
        );

        public function get($name) {
                if(! array_key_exists($name, $this->settings)) die("FOUT!\n");
                return $this->settings[$name];
        }

        function __construct($configuration = array()) {
                $this->settings = array_merge($this->defaults,$configuration);
        }
}
?>
