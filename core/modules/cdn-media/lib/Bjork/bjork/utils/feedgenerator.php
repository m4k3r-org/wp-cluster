<?php

namespace bjork\utils\feedgenerator;

use urllib;

use bjork\utils\encoding,
    bjork\utils\xmlutils\SimplerXMLGenerator;

function rfc2822_date(\DateTime $date) {
    return $date->format(\DateTime::RFC2822);
}

function rfc3339_date(\DateTime $date) {
    return $date->format(\DateTime::RFC3339);
}

function get_tag_uri($url, $date) {
    $bits = urllib::parse($url);
    $d = '';
    if (!empty($date))
        $d = sprintf(',%s', $date->format('Y-m-d'));
    return sprintf('tag:%s%s:%s/%s', $bits['hostname'], $d, $bits['path'], $bits['fragment']);
}

function getattr(array &$array, $name, $default=null) {
    if (array_key_exists($name, $array)) {
        $val = $array[$name];
        unset($array[$name]);
        if (!empty($val))
            return $val;
    }
    return $default;
}

function to_unicode($s) {
    return encoding::to_unicode($s, true);
}

/**
* Represents a feed item enclosure.
*/
class Enclosure {
    var $length, $mimeType, $url;
    
    function __construct($url, $length, $mimeType) {
        // All args are expected to be Unicode strings
        $this->length = $length;
        $this->mimeType = $mimeType;
        $this->url = encoding::iri_to_uri($url);
    }
}

/**
* Base class for all syndication feeds. Subclasses should provide write().
*/
abstract class SyndicationFeed {
    
    var $feed, $items;
    
    function __construct($title, $description, $link, array $options) {
        $this->items = array();
        
        $categories = getattr($options, 'categories');
        if (!empty($categories))
            $categories = array_map('bjork\utils\encoding::to_unicode', $categories);
        else
            $categories = array();
        
        $ttl = getattr($options, 'ttl');
        if (!is_null($ttl))
            $ttl = encoding::to_unicode($ttl);
        
        $this->feed = array_merge(array(
            'title' => to_unicode($title),
            'link' => encoding::iri_to_uri($link),
            'description' => to_unicode($description),
            'language' => to_unicode(getattr($options, 'language')),
            'authorEmail' => to_unicode(getattr($options, 'authorEmail')),
            'authorName' => to_unicode(getattr($options, 'authorName')),
            'authorLink' => encoding::iri_to_uri(getattr($options, 'authorLink')),
            'subtitle' => to_unicode(getattr($options, 'subtitle')),
            'categories' => $categories,
            'url' => encoding::iri_to_uri(getattr($options, 'url')),
            'copyright' => to_unicode(getattr($options, 'copyright')),
            'id' => getattr($options, 'guid', $link),
            'ttl' => $ttl,
        ), $options);
    }
    
    /**
    * Adds an item to the feed. All args are expected to be Unicode
    * strings except pubdate, which is a DateTime object, and
    * enclosure, which is an instance of the Enclosure class.
    */
    function addItem($title, $description, $link, array $options) {
        $categories = getattr($options, 'categories');
        if (!empty($categories))
            $categories = array_map('bjork\utils\encoding::to_unicode', $categories);
        else
            $categories = array();
        
        $ttl = getattr($options, 'ttl');
        if (!is_null($ttl))
            $ttl = encoding::to_unicode($ttl);
        
        $item = array_merge(array(
            'title' => to_unicode($title),
            'link' => encoding::iri_to_uri($link),
            'description' => to_unicode($description),
            'authorEmail' => to_unicode(getattr($options, 'authorEmail')),
            'authorName' => to_unicode(getattr($options, 'authorName')),
            'authorLink' => encoding::iri_to_uri(getattr($options, 'authorLink')),
            'pubdate' => getattr($options, 'pubdate'),
            'comments' => to_unicode(getattr($options, 'comments')),
            'guid' => to_unicode(getattr($options, 'guid')),
            'enclosure' => getattr($options, 'enclosure'),
            'categories' => $categories,
            'copyright' => to_unicode(getattr($options, 'copyright')),
            'ttl' => $ttl,
        ), $options);
        
        $this->items[] = $item;
    }
    
    function numItems() {
        return count($this->items);
    }
    
    function getLatestPostDate() {
        $updates = array();
        foreach ($this->items as $item) {
            if (!is_null($item['pubdate']))
                $updates[] = $item['pubdate'];
        }
        if (count($updates) > 0) {
            sort($updates);
            return end($updates);
        }
        return new \DateTime('now');
    }
    
    
    // xml -------------------------------------------------------------------
    
    abstract function getMIMEType();
    
    /**
    * Return extra attributes to place on the root (i.e. feed/channel)
    * element. Called from write().
    */
    protected function rootAttributes() {
        return array();
    }
    
    /**
    * Add elements in the root (i.e. feed/channel) element. Called
    * from write().
    */
    protected function addRootElements($handler) {
        // pass
    }
    
    /**
    * Return extra attributes to place on each item (i.e. item/entry) element.
    */
    protected function itemAttributes($item) {
        return array();
    }
    
    /**
    * Add elements on each item (i.e. item/entry) element.
    */
    protected function addItemElements($handler, $item) {
        // pass
    }
    
    // output ----------------------------------------------------------------
    
    abstract function write($outfile, $encoding);
    
    function writeString($encoding) {
        return $this->write(null, $encoding);
    }
}

abstract class RssFeed extends SyndicationFeed {
    
    abstract function getVersion();
    
    public function getMIMEType() {
        return 'application/rss+xml; charset=utf-8';
    }
    
    function write($outfile, $encoding) {
        $handler = new SimplerXMLGenerator($encoding, true);
        $handler->startDocument();
            $handler->startElement('rss', $this->rssAttributes());
                $handler->startElement('channel', $this->rootAttributes());
                    $this->addRootElements($handler);
                    $this->writeItems($handler);
                $handler->endElement(); // channel
            $handler->endElement(); // rss
        $handler->endDocument();
        
        // @FIXME: temporary hack. fix when HttpResponse
        // becomes a stream wrapper
        $outfile->write($handler->outputMemory());
    }
    
    function writeItems($handler) {
        foreach ($this->items as $item) {
            $handler->startElement('item', $this->itemAttributes($item));
            $this->addItemElements($handler, $item);
            $handler->endElement();
        }
    }
    
    function rssAttributes() {
        return array(
            'version' => $this->getVersion(),
            'xmlns:atom' => 'http://www.w3.org/2005/Atom',
        );
    }
    
    function addRootElements($handler) {
        $handler->addQuickElement('title', $this->feed['title']);
        $handler->addQuickElement('link', $this->feed['link']);
        $handler->addQuickElement('description', $this->feed['description']);
        if (!is_null($this->feed['url']))
            $handler->addQuickElement('atom:link', null, array(
                'rel' => 'self',
                'href' => $this->feed['url']));
        if (!is_null($this->feed['language']))
            $handler->addQuickElement('language', $this->feed['language']);
        foreach ($this->feed['categories'] as $cat)
            $handler->addQuickElement('category', $cat);
        if (!is_null($this->feed['copyright']))
            $handler->addQuickElement('copyright', $this->feed['copyright']);
        $handler->addQuickElement('lastBuildDate', rfc2822_date($this->getLatestPostDate()));
        if (!is_null($this->feed['ttl']))
            $handler->addQuickElement('ttl', $this->feed['ttl']);
    }
}

class RssUserland091Feed extends RssFeed {
    
    function getVersion() {
        return '0.91';
    }
    
    function addItemElements($handler, $item) {
        $handler->addQuickElement('title', $item['title']);
        $handler->addQuickElement('link', $item['link']);
        if (!is_null($item['description']))
            $handler->addQuickElement('description', $item['description']);
    }
}

class Rss201rev2Feed extends RssFeed {
    
    function getVersion() {
        return '2.0';
    }
    
    function addItemElements($handler, $item) {
        $handler->addQuickElement('title', $item['title']);
        $handler->addQuickElement('link', $item['link']);
        if (!is_null($item['description']))
            $handler->addQuickElement('description', $item['description']);
        
        if (!empty($item['authorName']) && !empty($item['authorEmail']))
            $handler->addQuickElement('author', "{$item['authorEmail']} ({$item['authorName']})");
        else if (!empty($item['authorEmail']))
            $handler->addQuickElement('author', $item['authorEmail']);
        else if (!empty($item['authorName']))
            $handler->addQuickElement('dc:creator', $item['authorName'], array(
                'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
        
        if (!is_null($item['pubdate']))
            $handler->addQuickElement('pubDate', rfc2822_date($item['pubdate']));
        if (!is_null($item['comments']))
            $handler->addQuickElement('comments', $item['comments']);
        if (!is_null($item['guid']))
            $handler->addQuickElement('guid', $item['guid']);
        if (!is_null($item['ttl']))
            $handler->addQuickElement('ttl', $item['ttl']);
        
        // enclosure
        if (!is_null($item['enclosure']))
            $handler->addQuickElement('enclosure', '', array(
                'url' => $item['enclosure']->url,
                'length' => $item['enclosure']->length,
                'type' => $item['enclosure']->mimeType,
            ));
        
        // categories
        foreach ($item['categories'] as $cat)
            $handler->addQuickElement('category', $cat);
    }
}

class Atom1Feed extends SyndicationFeed {
    
    public function getMIMEType() {
        return 'application/atom+xml; charset=utf-8';
    }
    
    function getNS() {
        return 'http://www.w3.org/2005/Atom';
    }
    
    function rootAttributes() {
        if ($this->feed['language'] !== null)
            return array('xmlns' => $this->getNS(), 'xml:lang' => $this->feed['language']);
        else
            return array('xmlns' => $this->getNS());
    }
    
    function write($outfile, $encoding) {
        $handler = new SimplerXMLGenerator($encoding, true);
        $handler->startDocument();
            $handler->startElement('feed', $this->rootAttributes());
                $this->addRootElements($handler);
                $this->writeItems($handler);
            $handler->endElement(); // feed
        $handler->endDocument();
        
        // @FIXME: temporary hack. fix when HttpResponse
        // becomes a stream wrapper
        $outfile->write($handler->outputMemory());
    }
    
    function writeItems($handler) {
        foreach ($this->items as $item) {
            $handler->startElement('entry', $this->itemAttributes($item));
            $this->addItemElements($handler, $item);
            $handler->endElement();
        }
    }
    
    function addRootElements($handler) {
        $handler->addQuickElement('title', $this->feed['title']);
        $handler->addQuickElement('link', '', array(
            'rel' => 'alternate',
            'href' => $this->feed['link']));
        if (!is_null($this->feed['url']))
            $handler->addQuickElement('link', '', array(
                'rel' => 'self',
                'href' => $this->feed['url']));
        $handler->addQuickElement('id', $this->feed['id']);
        $handler->addQuickElement('updated', rfc3339_date($this->getLatestPostDate()));
        
        if (!is_null($this->feed['authorName'])) {
            $handler->startElement('author');
                $handler->addQuickElement('name', $this->feed['authorName']);
                if (!is_null($this->feed['authorEmail']))
                    $handler->addQuickElement('email', $this->feed['authorEmail']);
                if (!is_null($this->feed['authorLink']))
                    $handler->addQuickElement('uri', $this->feed['authorLink']);
            $handler->endElement();
        }
        
        if (!is_null($this->feed['subtitle']))
            $handler->addQuickElement('subtitle', $this->feed['subtitle']);
        foreach ($this->feed['categories'] as $cat)
            $handler->addQuickElement('category', '', array('term' => $cat));
        if (!is_null($this->feed['copyright']))
            $handler->addQuickElement('rights', $this->feed['copyright']);
    }
    
    function addItemElements($handler, $item) {
        $handler->addQuickElement('title', $item['title']);
        $handler->addQuickElement('link', '', array(
            'rel' => 'alternate',
            'href' => $item['link']));
        if (!is_null($item['pubdate']))
            $handler->addQuickElement('updated', rfc3339_date($item['pubdate']));
        
        // author information
        if (!is_null($item['authorName'])) {
            $handler->startElement('author');
                $handler->addQuickElement('name', $item['authorName']);
                if (!is_null($item['authorEmail']))
                    $handler->addQuickElement('email', $item['authorEmail']);
                if (!is_null($item['authorLink']))
                    $handler->addQuickElement('uri', $item['authorLink']);
            $handler->endElement();
        }
        
        // unique id
        if (!is_null($item['guid']))
            $uniqueID = $item['guid'];
        else
            $uniqueID = get_tag_uri($item['link'], $item['pubdate']);
        $handler->addQuickElement('id', $uniqueID);
        
        // summary
        if (!is_null($item['description']))
            $handler->addQuickElement('summary', $item['description'], array(
                'type' => 'html',
            ));
        
        // enclosure
        if (!is_null($item['enclosure']))
            $handler->addQuickElement('link', '', array(
                'rel' => 'enclosure',
                'href' => $item['enclosure']->url,
                'length' => $item['enclosure']->length,
                'type' => $item['enclosure']->mimeType,
            ));
        
        // categories
        foreach ($item['categories'] as $cat)
            $handler->addQuickElement('category', '', array('term' => $cat));
        
        // copyright
        if (!is_null($item['copyright']))
            $handler->addQuickElement('rights', $item['copyright']);
    }
}

class DefaultFeed extends Rss201rev2Feed {}
