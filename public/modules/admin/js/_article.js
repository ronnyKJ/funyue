define(['bootstrap', 'modalLayer'], function(_bs, ModalAritcle){

  return function article() {

    var handlers = {
      fetch: function(e) {
        $.post('/api/article/fetch', function(res){
          window.location.reload();
        });
      },
      create: function(e) {
        modalAritcle.create();
      },
      edit: function(e, id) {
        modalAritcle.update(id);
      },

      hide: function(e, data) {
        var method  = +data == 1 ? 'hide' : 'show';
        var $target = $(e.target);
        var id      = $target.parents('tr[data-id]').data('id');
        $.post('/api/article/hide/', {id: id, action: method}, function(res){
          $target.button('reset');
          if(res.data) {
            if(+data == 1) {
              $target.removeClass('btn-default');
              $target.addClass('btn-info');
              $target.data('params', 0);
              $target.text( '显示');
            } else {
              $target.removeClass('btn-info');
              $target.addClass('btn-default');
              $target.data('params', 1);
              $target.text( '隐藏' );
            }
          }
        });
      }
    };

    var modalAritcle = new ModalAritcle({
      'name': 'article',
      'url': '/admin/article/edit',
      // NOTICE: php 在 debug 模式时遇到没有传文件的 file 表单项时会返回错误： Notice: No file uploaded in Unknown on line 0 导致 $.ajax parseerror 错误。
      'onsubmit': function(data){
        if( !~(['', 'lebo', 'news']).indexOf( $('#inputFrom').val() ) ){
          alert('请检查合作方');
          return false;
        }
        window.location.reload();
      }
    });

    $('div.container').delegate('[data-action]', 'click', function(e){
      var action = $(this).data('action');
      var params = $(this).data('params');
      if($(this).attr('data-loading-text'))
        $(this).button('loading');
      handlers[action](e, params);
      return false;
    });

    $('#date-btn').click(function(){
      location.href = "/admin/article/type/show/date/" + $('#date-input').val();
    });
  }
});