<?php
// Wiki extension, https://github.com/datenstrom/yellow-extensions/tree/master/source/wiki

class YellowWiki {
    const VERSION = "0.8.11";
    public $yellow;         // access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("wikiLocation", "");
        $this->yellow->system->setDefault("wikiNewLocation", "@title");
        $this->yellow->system->setDefault("wikiPagesMax", "5");
        $this->yellow->system->setDefault("wikiPaginationLimit", "30");
    }

    // Handle page meta data
    public function onParseMeta($page) {
        if ($page===$this->yellow->page) {
            if ($page->get("layout")=="wikipages" && !$this->yellow->toolbox->isLocationArguments()) {
                $page->set("layout", $page->isExisting("layoutShow") ? $page->get("layoutShow") : "wiki");
            }
        }
    }
    
    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if (substru($name, 0, 4)=="wiki" && ($type=="block" || $type=="inline")) {
            switch($name) {
                case "wikiauthors": $output = $this->getShorcutWikiauthors($page, $name, $text); break;
                case "wikipages":   $output = $this->getShorcutWikipages($page, $name, $text); break;
                case "wikichanges": $output = $this->getShorcutWikichanges($page, $name, $text); break;
                case "wikirelated": $output = $this->getShorcutWikirelated($page, $name, $text); break;
                case "wikitags":    $output = $this->getShorcutWikitags($page, $name, $text); break;
            }
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikiauthors($page, $name, $text) {
        $output = null;
        list($location, $pagesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($location)) $location = $this->yellow->system->get("wikiLocation");
        if (strempty($pagesMax)) $pagesMax = $this->yellow->system->get("wikiPagesMax");
        $wiki = $this->yellow->content->find($location);
        $pages = $this->getWikiPages($location);
        $page->setLastModified($pages->getModified());
        $authors = $this->getMeta($pages, "author");
        if (count($authors)) {
            $authors = $this->yellow->lookup->normaliseUpperLower($authors);
            if ($pagesMax!=0 && count($authors)>$pagesMax) {
                uasort($authors, "strnatcasecmp");
                $authors = array_slice($authors, -$pagesMax);
            }
            uksort($authors, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($authors as $key=>$value) {
                $output .= "<li><a href=\"".$wiki->getLocation(true).$this->yellow->toolbox->normaliseArguments("author:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikiauthors '$location' does not exist!");
        }
        return $output;
    }

    // Return wikiauthors shortcut
    public function getShorcutWikipages($page, $name, $text) {
        $output = null;
        list($location, $pagesMax, $author, $tag) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($location)) $location = $this->yellow->system->get("wikiLocation");
        if (strempty($pagesMax)) $pagesMax = $this->yellow->system->get("wikiPagesMax");
        $wiki = $this->yellow->content->find($location);
        $pages = $this->getWikiPages($location);
        if (!empty($author)) $pages->filter("author", $author);
        if (!empty($tag)) $pages->filter("tag", $tag);
        $pages->sort("title");
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
            if ($pagesMax!=0) $pages->limit($pagesMax);
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($pages as $pageWiki) {
                $output .= "<li><a".($pageWiki->isExisting("tag") ? " class=\"".$this->getClass($pageWiki)."\"" : "");
                $output .= " href=\"".$pageWiki->getLocation(true)."\">".$pageWiki->getHtml("title")."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikipages '$location' does not exist!");
        }
        return $output;
    }
        
    // Return wikiauthors shortcut
    public function getShorcutWikichanges($page, $name, $text) {
        $output = null;
        list($location, $pagesMax, $author, $tag) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($location)) $location = $this->yellow->system->get("wikiLocation");
        if (strempty($pagesMax)) $pagesMax = $this->yellow->system->get("wikiPagesMax");
        $wiki = $this->yellow->content->find($location);
        $pages = $this->getWikiPages($location);
        if (!empty($author)) $pages->filter("author", $author);
        if (!empty($tag)) $pages->filter("tag", $tag);
        $pages->sort("modified", false);
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
            if ($pagesMax!=0) $pages->limit($pagesMax);
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($pages as $pageWiki) {
                $output .= "<li><a".($pageWiki->isExisting("tag") ? " class=\"".$this->getClass($pageWiki)."\"" : "");
                $output .= " href=\"".$pageWiki->getLocation(true)."\">".$pageWiki->getHtml("title")."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikichanges '$location' does not exist!");
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikirelated($page, $name, $text) {
        $output = null;
        list($location, $pagesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($location)) $location = $this->yellow->system->get("wikiLocation");
        if (strempty($pagesMax)) $pagesMax = $this->yellow->system->get("wikiPagesMax");
        $wiki = $this->yellow->content->find($location);
        $pages = $this->getWikiPages($location);
        $pages->similar($page->getPage("main"));
        $page->setLastModified($pages->getModified());
        if (count($pages)) {
            if ($pagesMax!=0) $pages->limit($pagesMax);
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($pages as $pageWiki) {
                $output .= "<li><a".($pageWiki->isExisting("tag") ? " class=\"".$this->getClass($pageWiki)."\"" : "");
                $output .= " href=\"".$pageWiki->getLocation(true)."\">".$pageWiki->getHtml("title")."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikirelated '$location' does not exist!");
        }
        return $output;
    }
    
    // Return wikiauthors shortcut
    public function getShorcutWikitags($page, $name, $text) {
        $output = null;
        list($location, $pagesMax) = $this->yellow->toolbox->getTextArguments($text);
        if (empty($location)) $location = $this->yellow->system->get("wikiLocation");
        if (strempty($pagesMax)) $pagesMax = $this->yellow->system->get("wikiPagesMax");
        $wiki = $this->yellow->content->find($location);
        $pages = $this->getWikiPages($location);
        $page->setLastModified($pages->getModified());
        $tags = $this->getMeta($pages, "tag");
        if (count($tags)) {
            $tags = $this->yellow->lookup->normaliseUpperLower($tags);
            if ($pagesMax!=0 && count($tags)>$pagesMax) {
                uasort($tags, "strnatcasecmp");
                $tags = array_slice($tags, -$pagesMax);
            }
            uksort($tags, "strnatcasecmp");
            $output = "<div class=\"".htmlspecialchars($name)."\">\n";
            $output .= "<ul>\n";
            foreach ($tags as $key=>$value) {
                $output .= "<li><a href=\"".$wiki->getLocation(true).$this->yellow->toolbox->normaliseArguments("tag:$key")."\">";
                $output .= htmlspecialchars($key)."</a></li>\n";
            }
            $output .= "</ul>\n";
            $output .= "</div>\n";
        } else {
            $page->error(500, "Wikitags '$location' does not exist!");
        }
        return $output;
    }
    
    // Handle page layout
    public function onParsePageLayout($page, $name) {
        if ($name=="wikipages") {
            $chronologicalOrder = false;
            $pages = $this->getWikiPages($this->yellow->page->location);
            $pagesFilter = array();
            if ($page->getRequest("special")=="pages") {
                array_push($pagesFilter, $this->yellow->language->getText("wikiSpecialPages"));
            }
            if ($page->getRequest("special")=="changes") {
                $chronologicalOrder = true;
                array_push($pagesFilter, $this->yellow->language->getText("wikiSpecialChanges"));
            }
            if ($page->isRequest("tag")) {
                $pages->filter("tag", $page->getRequest("tag"));
                array_push($pagesFilter, $pages->getFilter());
            }
            if ($page->isRequest("author")) {
                $pages->filter("author", $page->getRequest("author"), false);
                array_push($pagesFilter, $pages->getFilter());
            }
            if ($page->isRequest("modified")) {
                $pages->filter("modified", $page->getRequest("modified"), false);
                array_push($pagesFilter, $this->yellow->language->normaliseDate($pages->getFilter()));
            }
            $pages->sort($chronologicalOrder ? "modified" : "title", $chronologicalOrder);
            $pages->pagination($this->yellow->system->get("wikiPaginationLimit"));
            if (!$pages->getPaginationNumber()) $this->yellow->page->error(404);
            if (!empty($pagesFilter)) {
                $text = implode(" ", $pagesFilter);
                $this->yellow->page->set("titleHeader", $text." - ".$this->yellow->page->get("sitename"));
                $this->yellow->page->set("titleContent", $this->yellow->page->get("title").": ".$text);
                $this->yellow->page->set("title", $this->yellow->page->get("title").": ".$text);
                $this->yellow->page->set("wikipagesChronologicalOrder", $chronologicalOrder);
            }
            $this->yellow->page->setPages("wiki", $pages);
            $this->yellow->page->setLastModified($pages->getModified());
            $this->yellow->page->setHeader("Cache-Control", "max-age=60");
        }
        if ($name=="wiki") {
            $location = $this->yellow->system->get("wikiLocation");
            if (empty($location)) $location = $this->yellow->lookup->getDirectoryLocation($this->yellow->page->location);
            $wiki = $this->yellow->content->find($location);
            $this->yellow->page->setPage("wiki", $wiki);
        }
    }
    
    // Handle content file editing
    public function onEditContentFile($page, $action, $email) {
        if ($page->get("layout")=="wiki") $page->set("pageNewLocation", $this->yellow->system->get("wikiNewLocation"));
    }
    
    // Return wiki pages
    public function getWikiPages($location) {
        $pages = $this->yellow->content->clean();
        $wiki = $this->yellow->content->find($location);
        if ($wiki) {
            if ($location==$this->yellow->system->get("wikiLocation")) {
                $pages = $this->yellow->content->index(!$wiki->isVisible());
            } else {
                $pages = $wiki->getChildren(!$wiki->isVisible());
            }
            $wiki->set("layout", $wiki->isExisting("layoutShow") ? $wiki->get("layoutShow") : "wiki");
            $pages->append($wiki)->filter("layout", "wiki");
        }
        return $pages;
    }
    
    // Return class for page
    public function getClass($page) {
        if ($page->isExisting("tag")) {
            foreach (preg_split("/\s*,\s*/", $page->get("tag")) as $tag) {
                $class .= " tag-".$this->yellow->toolbox->normaliseArguments($tag, false);
            }
        }
        return trim($class);
    }
    
    // Return meta data from page collection
    public function getMeta($pages, $key) {
        $data = array();
        foreach ($pages as $page) {
            if ($page->isExisting($key)) {
                foreach (preg_split("/\s*,\s*/", $page->get($key)) as $entry) {
                    ++$data[$entry];
                }
            }
        }
        return $data;
    }
}
