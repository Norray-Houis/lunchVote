<?phpdefined('BASEPATH') or exit('No direct script access allowed');/** * 分页 *  * 该类需要使用bootstrap，ajax分页，普通链接待完善 *  * @author william * Sep 21, 2017 */class Pages{    /**     * url     * @var string     */    public $url = '';        /**     * 数据量     * @var unknown     */    public $total_rows = 0;    /**     * 总页数     * @var integer     */    public $total_pages = 0;        /**     * 当前页     * @var unknown     */    public $cur_page = 1;        /**     * 每页显示数量     * @var unknown     */    public $limit = 10;        /**     * 偏移量     * @var unknown     */    public $offset = 2;        /**     * 最大页码     * @var unknown     */    private $max_page_num;        /**     * 最小页码     * @var unknown     */    private $min_page_num;    /**     * Constructor     * @param array $config     */    public function __construct($config = [])    {        if (! empty($config) && is_array($config)) {            foreach ($config as $k => $v) {                if (isset($this->$k)) {                    $this->$k = $v;                }            }        }                $this->init();    }        /**     * 初始化     */    private function init()    {        // 页数        $this->total_pages = ceil($this->total_rows / $this->limit);                // 最大最小页码        $this->min_page_num = $this->cur_page - $this->offset > 0 ? $this->cur_page - $this->offset : 1;        $this->max_page_num = $this->cur_page + $this->offset < $this->total_pages + 1 ? $this->cur_page + $this->offset : $this->total_pages;    }    /**     * 创建链接     */    public function create_link()    {        // 没有数据        if ($this->total_pages < 1) {//            exit('x12');            return;        }                // 页码        $digital_pages = '';                for ($num = $this->min_page_num; $num <= $this->max_page_num; $num ++) {            $digital_pages .= sprintf("<li%s><a href='javascript:void(0);' data-page='%d' >%d</a></li>", ($num == $this->cur_page ? " class='active'" : ""), $num, $num);        }                $pages = sprintf(            "<ul class='pagination'>                <li%s><a href='javascript:void(0);' data-page='%d'>首页</a></li>                            <li%s><a href='javascript:void(0);' data-page='%d'>上一页</a></li>                %s                <li%s><a href='javascript:void(0);' data-page='%d'>下一页</a></li>                <li%s><a href='javascript:void(0);' data-page='%d'>尾页</a></li>            </ul>",            ($this->cur_page == 1 ? ' class="disabled"' : ''), 1,             ($this->cur_page == 1 ? ' class="disabled"' : ''), ($this->cur_page - 1 > 0 ? $this->cur_page - 1 : 1),             $digital_pages,             ($this->cur_page == $this->total_pages ? ' class="disabled"' : ''), ($this->cur_page < $this->total_pages ? $this->cur_page + 1 : $this->total_pages),             ($this->cur_page == $this->total_pages ? ' class="disabled"' : ''), $this->total_pages        );                return $pages;    }}