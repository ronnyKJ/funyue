<div class="affix pe-sidebar">
    <ul class="nav nav-pills nav-stacked">
        <li<?php if(in_array('article', $this->uri->segment_array())):?> class="active"<?php endif?>><a href="/admin/article">文章</a></li>
        
        <li<?php if(in_array('cover', $this->uri->segment_array())):?> class="active"<?php endif?>><a href="/admin/cover/show/all">每日封面</a></li>
        <!--<li<?php if(in_array('player', $this->uri->segment_array())):?> class="active"<?php endif?>><a href="/admin/player">球员</a></li>
        <li<?php if(in_array('card', $this->uri->segment_array())):?> class="active"<?php endif?>><a href="/admin/card">卡片管理</a></li>
        <li<?php if(in_array('query', $this->uri->segment_array())):?> class="active"<?php endif?>><a href="/admin/query">数据库操作</a></li>
    	-->
    </ul>
</div>