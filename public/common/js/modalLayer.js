define(['bootstrap', 'wysihtml5', 'datetimepicker'], function(){

  var ModalAritcle = function(opt){
    var that = this;
    that._id = 0;
    this.container = $('#modal_' + opt.name);
    if(this.container.length === 0) {
      throw "无效模块，请检查模块 ID 是否一致。"
    }
    this.name = opt.name;
    this.url = opt.url;
    this.form = this.container.find('form')[0];
    this.instance = this.container.modal({'show':false});
    if(typeof opt.onshown == 'function') {
      this.instance.on('shown.bs.modal', opt.onshown);
    }
    if(typeof opt.onhidden == 'function') {
      this.instance.on('hidden.bs.modal', opt.onhidden);
    }
    this.initEls();
    $(this.form).submit(function(e){
      e.preventDefault();
      that.submit(opt.onsubmit || "");
    });
  };

  ModalAritcle.prototype.create = function() {
    var that = this;
    that._id = 0;
    that.purge();
    that.instance.modal('show');
  }

  ModalAritcle.prototype.update = function(id) {
    var that = this;
    if(that._id == 0 || that._id != id) {
      that.purge();
      $.post(that.url + '/' + id, function(res){
        that.set(res.data[0]);
      });
    }
    that._id = id;
    that.instance.modal('show');
  };

  ModalAritcle.prototype.html_entity_decode = function(htmlentity) {
    return $('<div></div>').html(htmlentity).text();
  };

  ModalAritcle.prototype.set = function(data) {
    for(var key in this.controls) {
      if(key.match(/id$/)) {
        $('#' + this.controls[key].id).val(data[key]);
      } else if(this.controls[key].type == 'checkbox') {
        if(+data[key] === 1) {
          $('#' + this.controls[key].id).attr('checked', 1);
        }
      } else if($('#' + this.controls[key].id).data('editor') == true) {
        // this.editor.setValue(this.html_entity_decode(data[key]));
        this.editor.setValue(data[key]);
      } else {
        $('#' + this.controls[key].id).val(this.html_entity_decode(data[key]));
      }
    }
  };

  ModalAritcle.prototype.initEls = function() {
    this.controls = {};
    var i = 0;
    var item, editor;
    while(item = this.form.elements.item(i++)){
      if(item.type != 'button' && !!item.id) {
        this.controls[item.name] = {'id': item.id, 'name': item.name, 'type': item.type};
      }
      if($(item).data('editor') == true) {
        editor = item;
      }
      if($(item).data('type') == 'date') {
        // TODO: 日期控件暂时不能用 (Larry)
        $(item).parent('div').datetimepicker({
          pickTime: false
        });
      }
    }
    if(editor) {
      this._editor  = $(editor).wysihtml5();
      this.editor   = this._editor.data('wysihtml5').editor;
    }
  };

  ModalAritcle.prototype.purge = function() {
    var $currentControl;
    for(var key in this.controls) {
      $currentControl = $('#' + this.controls[key].id);
      if(this.controls[key].type == 'checkbox') {
        $currentControl.removeAttr('checked');
      } else if($currentControl.data('editor') == true) {
        // NOTICE: 如何更改 wysihtml5 中的内容：https://github.com/jhollingworth/bootstrap-wysihtml5/issues/52
        this.editor.setValue('');
      } else {
        if($currentControl.data('default')) {
          $currentControl.val($currentControl.data('default'));
        } else {
          $currentControl.val('');
        }
      }
    }
  };

  ModalAritcle.prototype.submit = function(cb) {
    // var form = new FormData(this.form);
    form = $(this.form).serialize();
    var url;
    if(this._id == 0) {
      url = '/api/' + this.name + '/create';
    } else {
      url = '/api/' + this.name + '/update';
    }
    $.ajax({
      data: form,
      // cache: false,
      // contentType: false,
      // processData: false,
      type: 'POST',
      url: url,
      success: cb || null
    });
  };

  return ModalAritcle;

});