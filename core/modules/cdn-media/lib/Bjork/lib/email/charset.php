<?php

namespace email\charset {

use email\charset as base;

class Charset {
    
    protected
        $input_charset,
        $input_codec,
        $output_charset,
        $output_codec,
        $header_encoding,
        $body_encoding;
    
    function __construct($input_charset=charset::DEFAULT_CHARSET) {
        $input_charset = strtolower($input_charset);
        $this->input_charset = base::get_alias($input_charset, $input_charset);
        // We can try to guess which encoding and conversion to use by the
        // charset_map dictionary. Try that first, but let the user override
        // it.
        list($henc, $benc, $conv) = base::get_charset($this->input_charset);
        if (!$conv)
            $conv = $this->input_charset;
        // Set the attributes, allowing the arguments to override the default.
        $this->header_encoding = $henc;
        $this->body_encoding = $benc;
        $this->output_charset = base::get_alias($conv, $conv);
        $this->input_codec = base::get_codec($this->input_charset, $this->input_charset);
        $this->output_codec = base::get_codec($this->output_charset, $this->output_charset);
    }
    
    function __toString() {
        return strtolower($this->input_charset);
    }
    
    public function getInputCharset() {
        return $this->input_charset;
    }
    
    public function getOutputCharset() {
        return $this->output_charset ?: $this->input_charset;
    }
    
    public function getMIMEOutputCharset() {
        return mb_preferred_mime_name($this->getOutputCharset());
    }
    
    public function getInputCodec() {
        return $this->input_codec;
    }
    
    public function getOutputCodec() {
        return $this->output_codec;
    }
    
    public function getHeaderEncoding() {
        return $this->header_encoding;
    }
    
    public function getBodyEncoding() {
        return $this->body_encoding;
    }
    
    /**
    * Return the content-transfer-encoding used for body encoding.
    * 
    * This is either the string `quoted-printable' or `base64' depending on
    * the encoding used, or it is null in which case you should call
    * the charset::encode_7or8bit function with a single argument,
    * the Message object being encoded. The function should then set the
    * Content-Transfer-Encoding header itself to whatever is appropriate.
    * 
    * Returns "quoted-printable" if self.body_encoding is QP.
    * Returns "base64" if self.body_encoding is BASE64.
    * Returns null otherwise.
    */
    public function getBodyTransferEncoding() {
        $benc = null;
        if (base::QP === $this->body_encoding)
            $benc = 'quoted-printable';
        else if (base::BASE64 === $this->body_encoding)
            $benc = 'base64';
        else if (base::SHORTEST === $this->body_encoding)
            throw new \InvalidArgumentException('SHORTEST not allowed for body_enc');
        return $benc;
    }
    
    /**
    * Convert a string from the input_codec to the output_codec.
    */
    public function convert($s) {
        if ($this->input_codec != $this->output_codec)
            return mb_convert_encoding($s, $this->output_codec, $this->input_codec);
        return $s;
    }
    
    /**
    * Header-encode a string, optionally converting it to output_charset.
    * 
    * If convert is True, the string will be converted from the input
    * charset to the output charset automatically.  This is not useful for
    * multibyte character sets, which have line length issues (multibyte
    * characters must be split on a character, not a byte boundary); use the
    * high-level Header class to deal with these issues.  convert defaults
    * to False.
    * 
    * The type of encoding (base64 or quoted-printable) will be based on
    * self.header_encoding.
    */
    public function headerEncode($s, $convert=false) {
        $cset = $this->getOutputCharset();
        if ($convert)
            $s = $this->convert($s);
        if (base::BASE64 === $this->header_encoding)
            $transfer_encoding = 'B';
        else if (base::QP === $this->header_encoding)
            $transfer_encoding = 'Q';
        else if (base::SHORTEST === $this->header_encoding) {
            // @TODO: actually figure out both lengths and pick the
            //        one that produces the shortest string. for now
            //        set transfer_encoding to base64 as it's safer.
            $transfer_encoding = 'B';
        } else {
            // 7bit/8bit encodings return the string unchanged
            return $s;
        }
        return mb_encode_mimeheader($s, $cset, $transfer_encoding);
    }
    
    /**
    * Body-encode a string and convert it to output_charset.
    * 
    * If convert is True (the default), the string will be converted from
    * the input charset to output charset automatically.  Unlike
    * header_encode(), there are no issues with byte boundaries and
    * multibyte charsets in email bodies, so this is usually pretty safe.
    * 
    * The type of encoding (base64 or quoted-printable) will be based on
    * self.body_encoding.
    */
    public function bodyEncode($s, $convert=true) {
        if ($convert)
            $s = $this->convert($s);
        if (base::BASE64 === $this->body_encoding)
            $encoding = 'BASE64';
        else if (base::QP === $this->body_encoding)
            $encoding = 'Quoted-Printable';
        else
            // 7bit/8bit encodings return the string unchanged
            return $s;
        
        return mb_convert_encoding($s, $encoding);
    }
}

}

namespace email {

final class charset {
    
    // Flags for types of header encodings
    const QP       = 1; // Quoted-Printable
    const BASE64   = 2; // Base64
    const SHORTEST = 3; // the shorter of QP and base64, but only for headers
    
    // In "=?charset?q?hello_world?=", the =?, ?q?, and ?= add up to 7
    const MISC_LEN = 7;
    
    const DEFAULT_CHARSET = 'us-ascii';
    
    static
        // for these see self::init()
        $_CHARSETS = null,
        $_ALIASES = null,
        $_CODEC_MAP = null;
    
    /**
    * Add character set properties to the global registry.
    * 
    * charset is the input character set, and must be the canonical name of a
    * character set.
    * 
    * Optional header_enc and body_enc is either Charset.QP for
    * quoted-printable, Charset.BASE64 for base64 encoding, Charset.SHORTEST for
    * the shortest of qp or base64 encoding, or null for no encoding.  SHORTEST
    * is only valid for header_enc.  It describes how message headers and
    * message bodies in the input charset are to be encoded.  Default is no
    * encoding.
    * 
    * Optional output_charset is the character set that the output should be
    * in.  Conversions will proceed from input charset, to Unicode, to the
    * output charset when the method Charset.convert() is called.  The default
    * is to output in the same character set as the input.
    * 
    * Both input_charset and output_charset must have Unicode codec entries in
    * the module's charset-to-codec mapping; use add_codec(charset, codecname)
    * to add codecs the module does not know about.  See the codecs module's
    * documentation for more information.
    */
    public static function add_charset($charset, $header_enc=null, $body_enc=null, $output_charset=null) {
        self::init();
        if ($body_enc === self::SHORTEST)
            throw new \InvalidArgumentException('SHORTEST not allowed for body_enc');
        self::$_CHARSETS[$charset] = array($header_enc, $body_enc, $output_charset);
    }
    
    /**
    * Add a character set alias.
    * 
    * alias is the alias name, e.g. latin-1
    * canonical is the character set's canonical name, e.g. iso-8859-1
    */
    public static function add_alias($alias, $canonical) {
        self::init();
        self::$_ALIASES[$alias] = $canonical;
    }
    
    /**
    * Add a codec that map characters in the given charset to/from Unicode.
    * 
    * charset is the canonical name of a character set.  codecname is the name
    * of a Python codec, as appropriate for the second argument to the unicode()
    * built-in, or to the encode() method of a Unicode string.
    */
    public static function add_codec($charset, $codecname) {
        self::init();
        self::$_CODEC_MAP[$charset] = $codecname;
    }
    
    public static function get_charset($charset, array $default=null) {
        self::init();
        $charset_lower = strtolower($charset);
        if (array_key_exists($charset_lower, self::$_CHARSETS))
            return self::$_CHARSETS[$charset_lower];
        if (null === $default)
            return array(self::SHORTEST, self::BASE64, null);
        return $default;
    }
    
    public static function get_alias($charset, $default=null) {
        self::init();
        $charset_lower = strtolower($charset);
        if (array_key_exists($charset_lower, self::$_ALIASES))
            return self::$_ALIASES[$charset_lower];
        return $default;
    }
    
    public static function get_codec($charset, $default=null) {
        self::init();
        $charset_lower = strtolower($charset);
        if (array_key_exists($charset_lower, self::$_CODEC_MAP))
            return self::$_CODEC_MAP[$charset_lower];
        return $default;
    }
    
    static function init() {
        if (null !== self::$_CHARSETS)
            return;
        
        self::$_CHARSETS = array(
            // input                header enc      body enc     output conv
            'iso-8859-1'    => array(self::QP,      self::QP,      null),
            'iso-8859-2'    => array(self::QP,      self::QP,      null),
            'iso-8859-3'    => array(self::QP,      self::QP,      null),
            'iso-8859-4'    => array(self::QP,      self::QP,      null),
            // iso-8859-5 is Cyrillic, and not especially used
            // iso-8859-6 is Arabic, also not particularly used
            // iso-8859-7 is Greek, QP will not make it readable
            // iso-8859-8 is Hebrew, QP will not make it readable
            'iso-8859-9'    => array(self::QP,      self::QP,      null),
            'iso-8859-10'   => array(self::QP,      self::QP,      null),
            // iso-8859-11 is Thai, QP will not make it readable
            'iso-8859-13'   => array(self::QP,      self::QP,      null),
            'iso-8859-14'   => array(self::QP,      self::QP,      null),
            'iso-8859-15'   => array(self::QP,      self::QP,      null),
            'iso-8859-16'   => array(self::QP,      self::QP,      null),
            'windows-1252'  => array(self::QP,      self::QP,      null),
            'us-ascii'      => array(null,          null,          null),
            'big5'          => array(self::BASE64,  self::BASE64,  null),
            'gb2312'        => array(self::BASE64,  self::BASE64,  null),
            'euc-jp'        => array(self::BASE64,  null,          'iso-2022-jp'),
            'shift_jis'     => array(self::BASE64,  null,          'iso-2022-jp'),
            'iso-2022-jp'   => array(self::BASE64,  null,          null),
            'koi8-r'        => array(self::BASE64,  self::BASE64,  null),
            'utf-8'         => array(self::SHORTEST, self::BASE64, 'utf-8'),
            // We're making this one up to represent raw unencoded 8-bit
            '8bit'          => array(null,          self::BASE64,  'utf-8'),
        );
        
        // Aliases for other commonly-used names for character sets. Map
        // them to the real ones used in email.
        self::$_ALIASES = array(
            'latin_1'  => 'iso-8859-1',
            'latin-1'  => 'iso-8859-1',
            'latin_2'  => 'iso-8859-2',
            'latin-2'  => 'iso-8859-2',
            'latin_3'  => 'iso-8859-3',
            'latin-3'  => 'iso-8859-3',
            'latin_4'  => 'iso-8859-4',
            'latin-4'  => 'iso-8859-4',
            'latin_5'  => 'iso-8859-9',
            'latin-5'  => 'iso-8859-9',
            'latin_6'  => 'iso-8859-10',
            'latin-6'  => 'iso-8859-10',
            'latin_7'  => 'iso-8859-13',
            'latin-7'  => 'iso-8859-13',
            'latin_8'  => 'iso-8859-14',
            'latin-8'  => 'iso-8859-14',
            'latin_9'  => 'iso-8859-15',
            'latin-9'  => 'iso-8859-15',
            'latin_10' => 'iso-8859-16',
            'latin-10' => 'iso-8859-16',
            'ascii'    => 'us-ascii',
        );
        
        // Map charsets to their Unicode codec strings.
        self::$_CODEC_MAP = array(
            // Hack: We don't want *any* conversion for stuff marked us-ascii, as all
            // sorts of garbage might be sent to us in the guise of 7-bit us-ascii.
            // Let that stuff pass through without conversion to/from Unicode.
            'us-ascii'  => null,
        );
    }
}
    
}
