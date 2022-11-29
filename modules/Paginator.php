<?php

require_once $basePath . 'modules\helpers.php';

class Paginator implements \Iterator
{
    public $currentPage = 1;
    public $pageCount = 1;
    public $firstRecordNum = 1;

    private $existingParams;
    private $cur = 1;

    public function __construct(int $recordCount, array $existingParams = [])
    {
        $this->pageCount = ceil($recordCount /\Settings\COUNT_IMAGES_ON_PAGE);
        $pageNum = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        if ($pageNum < 1) {
            $pageNum = 1;
        }
        if ($pageNum > $this->pageCount) {
            $pageNum = $this->pageCount;
        }
        $this->currentPage = $pageNum;
        $this->firstRecordNum = ($pageNum - 1) * \Settings\COUNT_IMAGES_ON_PAGE;
        $this->existingParams = $existingParams;
    }

    public function current()
    {
        return \Helpers\getGETParams($this->existingParams, ['page' => $this->cur]);
    }

    public function key()
    {
        return $this->cur;
    }

    public function next()
    {
        $this->cur++;
    }

    public function rewind()
    {
        $this->cur = 1;
    }

    public function valid()
    {
        return $this->cur >= 1 && $this->cur <= $this->pageCount;
    }
}