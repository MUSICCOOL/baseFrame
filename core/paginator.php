<?php
/*
  Author: Xu Wenmeng
  Author Email: xuwenmeng@hotmail.com
  Description: 分页类
  Version: 1.0
*/

namespace system;

class paginator
{
    /** 当前页 */
    protected $currentPage;

    /** 最后一页 */
    protected $lastPage;

    /** 数据总数 */
    protected $total;

    /** 每页的数量 */
    protected $listRows;

    /** 显示的分页链接数 */
    protected $show_pages = 10;

    /** 分页链接的显示间隔 */
    protected $show_pages_step = 5;

    public function __construct($total, $listRows, $currentPage = null)
    {
        $this->total       = $total;
        $this->listRows    = $listRows;
        $this->currentPage = (empty($currentPage) || $currentPage < 0) ? 1 : $currentPage;
        $this->lastPage    = ceil($this->total / $this->listRows);
    }

    public function page()
    {
        if ($this->currentPage <= $this->show_pages - 1) {
            $perStart = 1;
            if ($this->show_pages <= $this->lastPage) {
                $perEnd = $this->show_pages;
            } else {
                $perEnd = $this->lastPage;
            }
        } else {
            if ($this->currentPage < $this->lastPage - $this->show_pages_step) {
                $perStart = $this->currentPage - ($this->show_pages - 1) + $this->show_pages_step;
                $perEnd   = $perStart + $this->show_pages - 1 + $this->show_pages_step;
            } else {
                $perStart = $this->lastPage - ($this->show_pages - 1);
                $perEnd   = $this->lastPage;
            }
        }
        $this->lastPage = empty($this->lastPage) ? 1 : $this->lastPage;
        $paginator      = [
            'total'       => $this->total,
            'pages'       => $this->lastPage,
            'currentPage' => $this->currentPage,
            'perStart'    => $perStart,
            'perEnd'      => $perEnd,
        ];
        return $paginator;
    }
}

