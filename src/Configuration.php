<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 11.08.2016
 * Time: 13:03
 */

namespace Profiler;


class Configuration
{
    const DEFAULT_NAME_SESSION = 'profiler';
    const XHPROF_ROOT = '/var/php_only/xhprof-0.9.4/';

    const TEMPLATE_OF_LINK_TO_IMG = 'http://xh.dev.tigr1991.ru/callgraph.php?run=run_id&source=name_of_session';
    const TEMPLATE_OF_LINK_TO_DETAIL_PAGE = 'http://xh.dev.tigr1991.ru/index.php?run=run_id&source=name_of_session';

}