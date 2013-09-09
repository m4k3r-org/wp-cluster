<?php

namespace bjork\contrib\syndication\views;

use strutils;

use bjork\conf\settings,
    bjork\core\exceptions\ObjectDoesNotExist,
    bjork\http\Http404,
    bjork\http\HttpResponse,
    bjork\template\Template,
    bjork\template\TemplateDoesNotExist,
    bjork\template\context\RequestContext,
    bjork\template\loader,
    bjork\utils\encoding,
    bjork\utils\feedgenerator\Enclosure,
    bjork\utils\feedgenerator\DefaultFeed,
    bjork\utils\html,
    bjork\utils\translation;

function add_domain($domain, $url, $is_secure=false) {
    if (!(strutils::startswith($url, 'http://') ||
          strutils::startswith($url, 'https://') ||
          strutils::startswith($url, 'mailto:')))
    {
        // 'url' must already be ASCII and URL-quoted, so no
        // need for encoding conversions here.
        if ($is_secure)
            $protocol = 'https';
        else
            $protocol = 'http';
        $url = encoding::iri_to_uri("{$protocol}://{$domain}{$url}");
    }
    return $url;
}

abstract class Feed {
    //
    // Options ---------------------------------------------------------------
    //
    
    /**
    * FEED TYPE -- Optional.
    * 
    * This should be a class that subclasses
    * bjork\utils\feedgenerator\SyndicationFeed. This designates which
    * type of feed this should be: RSS 2.0, Atom 1.0, etc. If you don't
    * specify $feedType, your feed will be RSS 2.0. This should return the
    * name of a class, not an instance of the class.
    * 
    * @return string
    */
    protected static function getFeedType() {
        return '\bjork\utils\feedgenerator\DefaultFeed';
    }
    
    /**
    * TEMPLATE NAMES -- Optional.
    * 
    * These should return strings representing names of Bjork templates that
    * the system should use in rendering the title and description of your
    * feed items. Both are optional. If a template is not specified, the
    * getItemTitle() or getItemDescription() methods are used instead.
    * 
    * @return string
    */
    protected static function getTitleTemplate() {
        return null;
    }
    
    protected static function getDescriptionTemplate() {
        return null;
    }
    
    
    //
    // Feed ------------------------------------------------------------------
    //
    
    /**
    * GET_OBJECT -- This is required for feeds that publish different data
    *               for different URL parameters.
    * 
    * Takes the current request and the arguments from the URL, and
    * returns an object represented by this feed. Throws
    * bjork\core\exceptions\ObjectDoesNotExist on error.
    *
    * @return mixed
    */
    protected function getObject($request, array $args) {
        return null;
    }
    
    
    /**
    * TITLE -- Required. The title of the feed.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's title as a normal PHP string.
    * 
    *   Eg.: 'foo'
    *
    * @return string
    */
    protected function getFeedTitle($obj) {
        return null;
    }
    
    
    /**
    * SUBTITLE -- Optional.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's subtitle as a normal PHP string.
    *
    * @return string
    */
    protected function getFeedSubtitle($obj) {
        return null;
    }
    
    
    /**
    * LINK -- Required. The URL of the feed.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's link as a normal PHP string.
    * 
    *   Eg.: '/foo/bar/'
    *
    * @return string
    */
    protected function getFeedLink($obj) {
        return null;
    }
    
    
    /**
    * DESCRIPTION -- Required. The description of the feed.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's description as a normal PHP string.
    * 
    *   Eg.: 'Foo bar baz.'
    *
    * @return string
    */
    protected function getFeedDescription($obj) {
        return null;
    }
    
    
    /**
    * URL -- Optional.
    * 
    * @return string
    */
    protected function getFeedURL($obj) {
        return null;
    }
    
    
    /**
    * GUID (only applicable to Atom feeds) -- Optional.
    * 
    * This property is only used for Atom feeds (where it is the
    * feed-level ID element). If not provided, the feed link is
    * used as the ID.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's globally unique ID as a normal PHP string.
    * 
    *   Eg.: '/foo/bar/1234'
    *
    * @return string
    */
    protected function getFeedGUID($obj) {
        return null;
    }
    
    
    /**
    * AUTHOR NAME -- Optional. The feed's author name.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's author name as a normal PHP string.
    * 
    *   Eg.: 'Sally Smith'
    *
    * @return string
    */
    protected function getFeedAuthorName($obj) {
        return null;
    }
    
    
    /**
    * AUTHOR EMAIL -- Optional. The feed's author email.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's author email as a normal PHP string.
    * 
    *   Eg.: 'test@example.com'
    *
    * @return string
    */
    protected function getFeedAuthorEmail($obj) {
        return null;
    }
    
    
    /**
    * AUTHOR LINK -- Optional. The feed's author link.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's author link as a normal PHP string.
    * 
    *   Eg.: 'http://www.example.com/'
    *
    * @return string
    */
    protected function getFeedAuthorLink($obj) {
        return null;
    }
    
    
    /**
    * CATEGORIES -- Optional. The feed's categories.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's categories as an iterable on strings.
    * 
    *   Eg.: array('php', 'bjork')
    *
    * @return string[]
    */
    protected function getFeedCategories($obj) {
        return null;
    }
    
    
    /**
    * COPYRIGHT NOTICE -- Optional.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's copyright notice as a normal PHP string.
    * 
    *   Eg.: 'Copyright (c) 2011, Sally Smith'
    *
    * @return string
    */
    protected function getFeedCopyright($obj) {
        return null;
    }
    
    
    /**
    * TTL -- Optional. The feed's time-to-live in seconds.
    * 
    * Takes the object returned by getObject() and returns the 
    * feed's time-to-live as an integer.
    * 
    *   Eg.: 'http://www.example.com/'
    *
    * @return int
    */
    protected function getFeedTTL($obj) {
        return null;
    }
    
    
    /**
    * ITEMS -- Required. The feed items.
    * 
    * Takes the object returned by getObject() and returns an
    * array of items to publish in this feed.
    * 
    *   Eg.: array('Item 1', 'Item 2')
    *
    * @return mixed[]
    */
    protected function getItems($obj) {
        return array();
    }
    
    
    //
    // Items -----------------------------------------------------------------
    //
    
    /**
    * TITLE & DESCRIPTION -- If getTitleTemplate() or getDescriptionTemplate()
    * return null, these are used instead. Both are optional, by default
    * they will use the string representation of the item.
    * 
    * Both take an item as returned by getItems() and return the 
    * items's title and description as a normal PHP string.
    * 
    *   Eg.: title --> 'Breaking News: Nothing Happening'
    *        description --> 'A description of the item.'
    *
    * @return string
    */
    protected function getItemTitle($item) {
        return empty($item) ? '' : html::escape(strval($item));
    }
    
    protected function getItemDescription($item) {
        return empty($item) ? '' : strval($item);
    }
    
    
    /**
    * LINK -- Required. The URL of the item.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's URL as a normal PHP string.
    *
    * @return string
    */
    protected function getItemLink($item) {
        return null;
    }
    
    
    /**
    * AUTHOR NAME -- Optional. The item's author name.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's author name as a normal PHP string.
    *
    * @return string
    */
    protected function getItemAuthorName($item) {
        return null;
    }
    
    
    /**
    * AUTHOR EMAIL -- Optional. The item's author email.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's author email as a normal PHP string.
    * 
    * If you specify this, you must specify getItemAuthorName().
    *
    * @return string
    */
    protected function getItemAuthorEmail($item) {
        return null;
    }
    
    
    /**
    * AUTHOR LINK -- Optional. The item's author email.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's author email as a normal PHP string.
    * 
    * If you specify this, you must specify getItemAuthorName().
    *
    * @return string
    */
    protected function getItemAuthorLink($item) {
        return null;
    }
    
    
    /**
    * ENCLOSURE URL -- Required if you're publishing enclosures.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's enclosure URL.
    * 
    *   Eg.: '/foo/bar.mp3'
    *
    * @return string
    */
    protected function getItemEnclosureURL($item) {
        return null;
    }
    
    
    /**
    * ENCLOSURE LENGTH -- Required if you're publishing enclosures.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's enclosure length.
    * 
    * The returned value should be either an integer, or a string
    * representation of the integer, in bytes.
    * 
    *   Eg.: 32000
    *
    * @return int
    */
    protected function getItemEnclosureLength($item) {
        return null;
    }
    
    
    /**
    * ENCLOSURE MIME TYPE -- Required if you're publishing enclosures.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's enclosure MIME type.
    * 
    *   Eg.: 'audio/mpeg'
    *
    * @return string
    */
    protected function getItemEnclosureMIMEType($item) {
        return null;
    }
    
    
    /**
    * PUB DATE -- Optional.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's pubdate.
    *
    * @return \DateTime
    */
    protected function getItemPubDate($item) {
        return null;
    }
    
    
    /**
    * CATEGORIES -- Optional.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's categories as an iterable on strings.
    * 
    *   Eg.: array('php', 'bjork')
    *
    * @return string[]
    */
    protected function getItemCategories($item) {
        return null;
    }
    
    
    /**
    * COPYRIGHT NOTICE (only applicable to Atom feeds) -- Optional.
    * 
    * Takes an item as returned by getItems() and returns the 
    * item's copyright notice as a normal PHP string.
    * 
    *   Eg.: 'Copyright (c) 2007, Sally Smith'
    *
    * @return string
    */
    protected function getItemCopyright($item) {
        return null;
    }
    
    
    /**
    * GUID -- Optional.
    *
    * @return string
    */
    protected function getItemGUID($item) {
        return null;
    }
    
    
    //
    // Feed generation -------------------------------------------------------
    //
    
    protected function getFeedExtraOptions($obj) {
        return array();
    }
    
    protected function getItemExtraOptions($item) {
        return array();
    }
    
    public function getFeed($obj, $request) {
        $domain = $request->getHost();
        $isHttps = $request->isSecure();
        
        // feed
        
        $link = $this->getFeedLink($obj);
        $link = add_domain($domain, $link, $isHttps);
        
        $feedURL = $this->getFeedURL($obj);
        $feedURL = add_domain($domain, $feedURL
            ? $feedURL : $request->getPath(),
            $isHttps);
        
        $options = array_merge(array(
            'url'         => $feedURL,
            'language'    => translation::get_language(),
            'subtitle'    => $this->getFeedSubtitle($obj),
            'authorName'  => $this->getFeedAuthorName($obj),
            'authorEmail' => $this->getFeedAuthorEmail($obj),
            'authorLink'  => $this->getFeedAuthorLink($obj),
            'categories'  => $this->getFeedCategories($obj),
            'copyright'   => $this->getFeedCopyright($obj),
            'guid'        => $this->getFeedGUID($obj),
            'ttl'         => $this->getFeedTTL($obj),
        ), $this->getFeedExtraOptions($obj));
        
        $feedType = static::getFeedType();
        $feed = new $feedType(
            $this->getFeedTitle($obj),
            $this->getFeedDescription($obj),
            $link,
            $options);
        
        // items
        
        $titleTpl = null;
        $titleTplPath = static::getTitleTemplate();
        if (!empty($titleTplPath)) {
            try {
                list($path, $name) = loader::find_template($titleTplPath);
                $titleTpl = new Template($path);
            } catch (TemplateDoesNotExist $e) {
                // pass
            }
        }
        
        $descriptionTpl = null;
        $descriptionTplPath = static::getDescriptionTemplate();
        if (!empty($descriptionTplPath)) {
            try {
                list($path, $name) = loader::find_template($descriptionTplPath);
                $descriptionTpl = new Template($path);
            } catch (TemplateDoesNotExist $e) {
                // pass
            }
        }
        
        foreach ($this->getItems($obj) as $item) {
            
            if (!empty($titleTpl) || !empty($descriptionTpl)) {
                $context = new RequestContext($request, array(
                    'obj' => $item,
                    'site' => $domain,
                ));
            }
            
            // title
            if (!empty($titleTpl))
                $title = $titleTpl->render($context);
            else
                $title = $this->getItemTitle($item);
            
            // description
            if (!empty($descriptionTpl))
                $description = $descriptionTpl->render($context);
            else
                $description = $this->getItemDescription($item);
            
            // link
            $link = add_domain($domain, $this->getItemLink($item), $isHttps);
            
            // enclosure
            $enc = null;
            $encURL = $this->getItemEnclosureURL($item);
            if (!empty($encURL)) {
                $enc = new Enclosure($encURL,
                    $this->getItemEnclosureLength($item),
                    $this->getItemEnclosureMIMEType($item)
                );
            }
            
            // author
            $authorName = $this->getItemAuthorName($item);
            if (!empty($authorName)) {
                $authorEmail = $this->getItemAuthorEmail($item);
                $authorLink = $this->getItemAuthorLink($item);
            } else {
                $authorEmail = null;
                $authorLink = null;
            }
            
            // guid
            $guid = $this->getItemGUID($item);
            if (!empty($guid))
                $guid = $link;
            
            $options = array_merge(array(
                'guid'        => $guid,
                'enclosure'   => $enc,
                'authorName'  => $authorName,
                'authorEmail' => $authorEmail,
                'authorLink'  => $authorLink,
                'pubdate'     => $this->getItemPubDate($item),
                'categories'  => $this->getItemCategories($item),
                'copyright'   => $this->getItemCopyright($item)
            ), $this->getItemExtraOptions($item));
            
            $feed->addItem($title, $description, $link, $options);
        }
        
        return $feed;
    }
    
    final public static function asView($request) {
        $args = func_get_args();
        array_shift($args);
        
        $self = new static();
        
        try {
            $obj = call_user_func_array(array($self, 'getObject'), array_merge(
                array($request), array($args)));
        } catch (ObjectDoesNotExist $e) {
            throw new Http404('Feed object does not exist.');
        }
        
        $feedgen = $self->getFeed($obj, $request);
        $response = new HttpResponse('', $feedgen->getMIMEType());
        $feedgen->write($response, 'utf-8');
        return $response;
    }
}
