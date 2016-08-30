<?php

namespace Profiler;

class Profiler
{




    protected $include_files = [
        'xhprof_lib/utils/xhprof_lib.php',
        '/xhprof_lib/utils/xhprof_runs.php',
    ];
    /** @var  \Profiler\Profiler */
    protected static $instance;
    /** @var  string */
    protected $name_of_session;
    /** @var  string */
    protected $last_link_of_img_of_call_graph;
    /** @var  string */
    protected $last_link_of_detail_page;

    /**
     * @return static
     */
    public static function getInstance()
    {
        static::checkEnvironment();

        if (static::$instance === null) {
            static::$instance = new static();
            return static::$instance;
        }
        return static::$instance;
    }

    protected function __construct()
    {

    }

    protected static function checkEnvironment()
    {
        if (!function_exists('xhprof_enable')) {
            throw new \Profiler\Exception("Попытка вызвать функцию xhprof_enable. Профайлер не установлен.");
        }
        if (!function_exists('xhprof_disable')) {
            throw new \Profiler\Exception('Попытка вызвать функцию xhprof_disable. Профайлер не установлен.');
        }
    }

    /**
     * @param string $name_of_session
     */
    public function start($name_of_session = null)
    {
        if ($name_of_session === null) {
            $name_of_session = \Profiler\Configuration::DEFAULT_NAME_SESSION. "_" . microtime(true);
        }
        assert(is_string($name_of_session));
        $this->name_of_session = $name_of_session;
        $this->xhprofEnable();
    }

    public function stop()
    {
        $xhprof_data = $this->xhprofDisable();
        $this->includeFiles();
        $xhprof_runs = $this->createXHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, $this->name_of_session);

        $this->last_link_of_img_of_call_graph = $this->generateLinkOfImgOfCallGraph($run_id, $this->name_of_session);
        $this->last_link_of_detail_page = $this->generateLinkOfDetailPage($run_id, $this->name_of_session);
    }

    protected function includeFiles()
    {
        foreach ($this->include_files as $include_file) {
            include_once \Profiler\Configuration::XHPROF_ROOT . $include_file;
        }
    }

    protected function xhprofEnable()
    {
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }

    protected function xhprofDisable()
    {
        $xhprof_data = xhprof_disable();
        return $xhprof_data;
    }

    /**
     * @return \XHProfRuns_Default
     */
    protected function createXHProfRuns_Default()
    {
        return new \XHProfRuns_Default();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getNameOfLastSession()
    {
        if ($this->name_of_session === null) {
            throw new \Profiler\Exception("Профайлер ещё ниразу не вызывался");
        }
        return $this->name_of_session;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getLastLinkOfImgOfCallGraph()
    {
        if ($this->last_link_of_img_of_call_graph === null) {
            throw new \Profiler\Exception("Профайлер ещё ниразу не вызывался");
        }
        return $this->last_link_of_img_of_call_graph;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getLastLinkOfDetailPage()
    {
        if ($this->last_link_of_detail_page === null) {
            throw new \Profiler\Exception("Профайлер ещё ниразу не вызывался");
        }
        return $this->last_link_of_detail_page;
    }

    /**
     * @param int $run_id
     * @param string $name_of_session
     * @return string
     */
    protected function generateLinkOfImgOfCallGraph($run_id, $name_of_session)
    {
        $data = [
            '#run_id#' => $run_id,
            '#name_of_session#' => $name_of_session,
        ];
        $result_link = preg_replace(array_keys($data), array_values($data), static::TEMPLATE_OF_LINK_TO_IMG);
        return $result_link;
    }

    /**
     * @param int $run_id
     * @param string $name_of_session
     * @return string
     */
    protected function generateLinkOfDetailPage($run_id, $name_of_session)
    {
        $data = [
            '#run_id#' => $run_id,
            '#name_of_session#' => $name_of_session,
        ];
        $result_link = preg_replace(array_keys($data), array_values($data), static::TEMPLATE_OF_LINK_TO_DETAIL_PAGE);
        return $result_link;
    }

}