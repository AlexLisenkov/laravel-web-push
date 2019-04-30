<?php

namespace AlexLisenkov\LaravelWebPush;

use Base64Url\Base64Url;
use Elliptic\EC;
use Orchestra\Testbench\TestCase;

class P256EncryptedMessageTest extends TestCase
{
    private $public_key = 'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';
    private $auth = 'auth';
    private $payload = '{payload: true}';
    private $salt = 'c8b1ca1d80b4513f72e119cf61638210';
    /**
     * @var P256EncryptedMessage
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $ec = new EC('p256');
        $subscriber_public_key = Base64Url::decode($this->public_key);
        $subscriber_auth_token = Base64Url::decode($this->auth);

        $subscriber_p256 = $ec->keyFromPublic(bin2hex($subscriber_public_key), 'hex');
        $server_p256 = $ec->keyFromPrivate(bin2hex('WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'));

        $this->subject = new P256EncryptedMessage($server_p256, $subscriber_p256, $subscriber_auth_token, hex2bin($this->salt),
            $this->payload);
    }

    public function testThatGetCypherIsExactlyWithPadLenght(): void
    {
        $expected = 3070;
        $actual = $this->subject->getCypherLength();

        $this->assertEquals($expected, $actual);
    }

    public function testGetCypher(): void
    {
        $expected = hex2bin('fc5df51842c8ed8d577805b627b8d83be5980db4b7966818bf3146927073b773bc974fc6bade05e3eb87f3ee53f38877c43d5ab716dddd2e89fff8db864fb0f3303de8d58c4161fb3c0918c02207bfb9ec1683811d1d82b16e2d5b68e82b77eeacae1b9f4ecd855fd4e82ed66c8b324f29ba5c76eea91386fd39566c3f9f950c47275c367fdfee160ccd223fead7bf23859184df33e2d14f2986d68f0d0bf4f086423908271a03791bbb29bd7550bd66f697e1b1a5e081a1d691ee6af3947d2bfcd36b0786358d1c60c9a46dea7438cdb7b117d3d60b99b860818df956d76554a4cab7dd2b700c155d3b41e4db2b50f9b77ead12cb5a35d2d4becf1e2c3b7a3505769e49100b958c3cbaa59beaf5ea378e0cfd0a79bea01e279fd14b6c799fd93463ac5d6607c86b76606355aac2f70ae867d50f1ecd824fcfad9058f0b86c1526e99fe21afea8a7bb20167d06b59c40a9c7ad21fdaea51ea0df6d576fbb27cfb209f6464d945d523ad376ccabeaa0e465fe230a99de79401861939d013609ef0c07a04cedb589ec26a18b4803c04a1fd83e220c9b2b9da56ca7d64cf819eebd1fb077bc53fb4d77e7a4c89e08815e0c3931a8204f06689a8ec2379068c98551fcb73ad02d100f0474bb38fcb5d3d8f3aa8ea4d054c820f09bded2c07c4f2218f7694cfde60211968b346ccd08b902d3fb06d37eab630971b5188ed5d80bb94a079ccbe4096d0d52b5698435378e432bee738fa453257fce1f9d23ce5ab7879984939b1760ac831d5916bd62ea11193c570be6a833e141b99ee5807753d3d002dc11a7a0fc646a66c2f781900fbaa60da5ef515f337a5b9d9c2f41e50be3a891f170d81c9c8ebe75850e8b7014f8694cdd6447db8d69802e2f7258d66d3f3622b1f4b22c9db54635c33b13865b805bb040a272474bde86f08356fa68a7afb8991f3a5ffa118ded72e7522dab7628c90742859a8346d315e16483db917712f0e3c1a8e75560b7bc5802de4e545e9b6a441ca273d856574e9ed92acaaf352a399042b85198ebed7dbe24a6fa61cd13b36447944536865aa15f6839fbbd36776d2d0f3ef7b8a3bfe9c269999e5d1381bb69a16460ea674b77e78cb3f871435a2f690320bfaa1c834d17de774494e07547cc3d3cd50f3c83f90f19ed7f72719296ddec0865a8569e8eabea38202153825d01c0d466a33b6225fa9d7b22daa1a9e517591efeca76863e8ace99de37c8082897f0cb8bf6ade8247a39012ed01f3d65e4d9cf9a574fed3f5ef8bcea67af02e22664b3494bbfd3de28b5c8c75a1697ec47ffbeb07e6516555b8ef12a5ec326ff50baca6445df2c94cee0a2abe1d876a5778184afc361a7680b06a46ba6727cbcb5a51d114d51ea4df45c8b73c9cc924939b10bad2166a7cff95f3bc3daf61e6ac750151f7ad0ccd7a180419273450b886db8296363317ab32c4959575193a6ffc6b429d432be8b26e2445a87f1177fe3897b3e3224645d017d377166e6c728d75440bcddb2651461a02d644c92802af5798b95d2b907bfc92d06ed1877c59f75392dc3dcc18c638234d3cfb1ff890209f84f9995793b14a0bd32f4a7fdd39e8cd3205a23abb500b07f94379a99c35c8caff94a84c84f1a21eb41e9cf1e348430427bd5899cbe3611f1e12fbe921c06eb4d548fc64508e698d926058e84954ef5b0bb0e3061e56cfa94fe612bb271f9017e739cdd74879209ee15e1671d809ee2b22b17ebaa416ea8e766c90ddc5f08afe75d02fb42fd1243ab6e8d79c9f44ce38107c794c8100992e3f8b7edcf12a5e6b5395ee9a3c2509b9622e66cdd81de44ae0de81b69eae4e0b672ebdf8cf3dca30e628d28367e534138862692d490fcb8eca8372951d33674dada69a65a0d564824b806f42a615724f2d6146279225e31e456a59d2c346d5f4c78818cc089051fa7f3aa6babe4f2b54426bcea53580d472d9778ddc8eac570e18c0317442b6037f639c397d73c12ec39999878a908229e95ac583083310af8127f811dde49117a255b5e8a2fd535f35fa943aebcfcb7004a625c9aae2404b9d3b3981da1420ec73474fdbc52694ac799efc0d55a9852c57fcdc1a11f0737821e53dc377bdfe9df21bc67351f3bf1ae8270adb9bee81302e7566a4be13f27697e03208971fe3e7b7d38d101cb958236fe81c8f6a743f10253ccc404c6bb5597ce7cded7f4738dd42d3b12c4911688d67f4256a639c4aa29119d0ebf5de8c8b02ef90288776db6e0d095dd3d03bf893ed08bd08abb4d1898a3c6282900f5c6368feb7402d49d5c5a5f0fdc44b25456458721339abc4804095af3dadbd582d4089fcd7e56c5ee65f68ce7d0f330225d8ed3cf17bbe42f4d273579008b954d3126c74b49e988654f8e89e3311e95f39724e73a4023af8e8d9dd4044be10696072c6975cf0710cd6ae958e1d4423e3a883d5add76eee807c1b92639bf1d2e1ca50469bd504ea7f5a7694c46dd2e00773391320264117630cff1e9add6576541c41196e502b5250c11e44560377b087db549c5307282e50d163d236f655de909eb583c38e0080f343844354054ec0b8572a6f4beb631c3d9266dff99191e3b0d071db46e35c1c1a042f9cbb026632a34b09898fe249f0ffb485f73efc5b6b64bdaf83d59bfa53f106e8181a473989eb88900196422c4a8636df96d3f7be2d817423aac2a6cc9f166f5999e639fe32874240e2fbee2c5b29fed888e06f02ddd78c3566207e1e440343d7c8127dca2929c8d547589ebee5630c74f0302ff25e06e8b7c783c50370863a58c1e4eed355127de01178dd717f3dc7cb6f28bc524fbfc1eadf0dd917c32a6ba142a2c5c14f7cbf7151255e658953d19339b222c212eefd2a07da05aa731f29633db31b3b60de571bb4b6fc435a84f01367e761c195c937ac07872230f91557fb5fdb25a10bbdef5eb0e27aada0f137c238e75ec97e60419695613a17ce2222b7cb916b9dd2aabb752bd05de169c08a32d4998e2eac96e640a54b282d4bc451f0ee0df6fc439195679782256e94b7c54854be0240e707809922d2659e9b71af56e1fcc2bfb9ba162bb509e3ff99383e43fb02f4c2e916e3bf462d55a083552d314a7d80164fba5ebfe7538371288a8a9ee6cf394888b0ee43097d9d90a916a02e3ca3deecba08f674c9b80ab36eee85542973e9ac3b2293f3d04c8f722aa199c52a57653b03095f31ba062f919f2f80539913b5a8b6248e8d1779ed65c70c7d31a8e1195b803296ec030b65727c0b2e5662d64a66d76f49d029e8631d1e91b4659e3938b8f57dff5e81ff2f194879c4e68fbd775c66ee6408510d34378043da0f77ab4a5cad1850d4fad4536c9d2e72697fcdb5f89816398ff0ab6efc418b78e0dbe5c577c71b738b554e27e10f9f00857e4faf0c9d247e3b92cb0b381ef0fd011336a836f016ffb1ba2210c7e8c53e0bffd34bcb3ac201e5423d0b98e826c5b81b0df1a2d3fa6fcf6d288a466a9e1530ec77969b5eeec6e33560e11dbec37f1fca8d4dcc6d4ea27f43613e71dea9a9a8ccd1635c8e1735bdb2d3b0780c86cdb46ede767253520fc7da0d5fd717eca48a5a0b84d4e41758b1f8dac0790e811ca82b7fb7aba471aa690daec1a03c573b232bd22ab1256b36bf368edb6b57adbd055c55d4db0812f26b0e899380edcde17264b97a8eb2d6d61d2464a46c8fabb0536b16508f98bfd85b2f947bfc524be83e1ef87705b3cd83008802e0fb376c8ce1d4e03c7ba96a3148f3ae0fefc7162e8a4d1403f7f9df9a24aa1e709b4da478b89a5345eb7433e76f3aef365ebf5e4207c6b4ce9095bfd2facbca9667aee94150eec57cfe3906108fb1fa1e0cedb1231489573232e8b72a48e0fa688f4779b42a6b59d500d1dfb2a9aac3edc44c455a3b418376d850216887c2c4475e6b2da5354fc1bc24f5cec78106b6ba0353052c4c9661f9b02a1b77b435326796561a2e094f1c6c66ae75ad4737d21b16b3a90f0a56132a4babef5faba8c4257de69069e000377a9c565b4dae5545dd9a108d998625a10b9454d090f0164fb73ae7c4b952cdbe673598b5b723a04d11d5d1c2f1c7e4abcc23494220d1b71e075bb22277bc0a927fdab02f34d353a5279669adc056d44948620220160ecf1dbfcbd5a7b6f1dfa8b3717c83aca26e89cf13018b80952b2c4c0d3e03a714c74768110ce01a99e7df888afc1ce5a57ed8d8647730241a5f59def3ec7f60f6fcccce7350a69e37b3a38c3594a114bf701833705040009a2a2bc998bf97fa0715ccb3e3d2f2882c6326229d439da8c45287529bbac22124a3d78307b7c78a8d71696f837f0245c');
        $actual = $this->subject->getCypher();

        $this->assertEquals($expected, $actual);
    }

    public function testGetContentEncryptionKey(): void
    {
        $expected = hex2bin('f26bf6ae31686e70d05e58dcbe68d3d0');
        $actual = $this->subject->getContentEncryptionKey();

        $this->assertEquals($expected, $actual);
    }

    public function testGetContext(): void
    {
        $expected = hex2bin('000041041a76652ae7369e462c16c1beef645cfcca5e0712a3c5f265734b8c106e5e4cbe229cc265248bc9f85328b3e9a37527945e94056823ceb83f72cddfe80a954c3e004104469f9ad6c69a5c28b54731b527d45020dc1893619f21a8da55bd2bfb65948d40e69416633182b9b2e8e0a17a0f3dd7a93545d926d6a4cde3228af19862cbaa02');
        $actual = $this->subject->getContext();

        $this->assertEquals($expected, $actual);
    }

    public function testGetPublicKey(): void
    {
        $expected = hex2bin('04469f9ad6c69a5c28b54731b527d45020dc1893619f21a8da55bd2bfb65948d40e69416633182b9b2e8e0a17a0f3dd7a93545d926d6a4cde3228af19862cbaa02');
        $actual = $this->subject->getPublicKey();

        $this->assertEquals($expected, $actual);
    }

    public function testGetPseudoRandomKey(): void
    {
        $expected = hex2bin('741f8667d29ab4183fdeaf3a4edde1dc5c3a91f9cd6e291ac1402153873314c9');
        $actual = $this->subject->getPseudoRandomKey();

        $this->assertEquals($expected, $actual);
    }

    public function testGetSharedSecret(): void
    {
        $expected = hex2bin('96e661654429e554ba2a708ecac30bce0653ffb5d9b026eaff45bfe39fdfa948');
        $actual = $this->subject->getSharedSecret();

        $this->assertEquals($expected, $actual);
    }

    public function testGetNonce(): void
    {
        $expected = hex2bin('ff952ce7e8b9417c68d2060e');
        $actual = $this->subject->getNonce();

        $this->assertEquals($expected, $actual);
    }

    public function testGetSalt(): void
    {
        $expected = hex2bin($this->salt);
        $actual = $this->subject->getSalt();

        $this->assertEquals($expected, $actual);
    }

    public function testGetEncodedSalt(): void
    {
        $expected = Base64Url::encode(hex2bin($this->salt));
        $actual = $this->subject->getEncodedSalt();

        $this->assertEquals($expected, $actual);
    }

    public function testGetPrivateKey(): void
    {
        $expected = hex2bin('881c374192aa942ff633d806d4784228b700fbaa844fea85589fa357b8c6e0fe');
        $actual = $this->subject->getPrivateKey();

        $this->assertEquals($expected, $actual);
    }

    public function testGetEncodedPrivateKey(): void
    {
        $expected = Base64Url::encode(hex2bin('881c374192aa942ff633d806d4784228b700fbaa844fea85589fa357b8c6e0fe'));
        $actual = $this->subject->getEncodedPrivateKey();

        $this->assertEquals($expected, $actual);
    }

    public function testGetEncodedPublicKey(): void
    {
        $expected = Base64Url::encode(hex2bin('04469f9ad6c69a5c28b54731b527d45020dc1893619f21a8da55bd2bfb65948d40e69416633182b9b2e8e0a17a0f3dd7a93545d926d6a4cde3228af19862cbaa02'));
        $actual = $this->subject->getEncodedPublicKey();

        $this->assertEquals($expected, $actual);
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelWebPushServiceProvider::class];
    }
}
