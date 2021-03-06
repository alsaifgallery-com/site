<?php
namespace MGS\ThemeSettings\Model\OptimizeWeb\Handle;

class Raw
{
    /** @var string */
    protected $content = null;
    /** @var int */
    protected $start = null;
    /** @var int */
    protected $end = null;

	/** @var array */
    protected $attributes = [];
    /**
     * @param string $content
     * @param int $start
     * @param int $end
     *
     * @throws \Exception
     */
    public function __construct($content, $start, $end)
    {
        if (!is_string($content)) {
            throw new \Exception('Finder\Result\Raw error: $content must be string');
        }
        if (!is_integer($start)) {
            throw new \Exception('Finder\Result\Raw error: $start must be integer');
        }
        if (!is_integer($end)) {
            throw new \Exception('Finder\Result\Raw error: $end must be integer');
        }
		$dom = new \DOMDocument();
        @$dom->loadHTML('<html><body>' . $content . '</body></html>');
        $xml = simplexml_import_dom($dom);
        $tag = $xml->xpath('body/*');
        foreach ($tag[0]->attributes() as $key => $value) {
            $this->attributes[$key] = $value->__toString();
        }
        $this->content = $content;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }
	
	/**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}