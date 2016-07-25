function HealtheSyndicationField(fieldName, containerId, medium, syndicationType, postID, wpAPINonce) {
  function select(selector, n) {
    if (n === undefined) {
      n = 1;
    }
    var selected = jQuery(selector);
    if (selected.length !== n) {
      throw "Expected " + n + " elements matching '" + selector
        + "' but found " + selected.length;
    }
    return selected
  }
  this.fieldName = fieldName;
  this.containerId = containerId;
  this.newButton = jQuery('<div class="button">New</div>');
  this.formContainer = select('#' + containerId);
  this.formCancelButton = select('#' + containerId + '-cancel');
  this.createButton = select('#' + containerId + '-create');
  this.noticeBox = select('#' + containerId + '-notice');
  this.spinner = select('#' + containerId + '-spinner');
  this.postTitleField = select('#titlediv input[name="post_title"]');
  this.syndicationTitleField = select('#' + containerId + ' input[name="post_title"]');
  this.outletField = select('#' + containerId + ' input[name="outlet"]');
  this.postID = postID;
  this.medium = medium;
  this.syndicationType = syndicationType;
  this.wpAPINonce = wpAPINonce;

  this.updateSyndicationTitle = function() {
    var outletFieldUI = select('#' + this.containerId + ' div.pods-form-ui-field-name-outlet');
    var title = outletFieldUI.text().trim() + ": " + this.postTitleField.val();
    this.syndicationTitleField.val(title);
  };

  this.disableCreate = function() {
    this.createButton.addClass('disabled');
  };

  this.enableCreate = function() {
    this.createButton.removeClass('disabled');
  };

  this.disableCancel = function() {
    this.formCancelButton.addClass('disabled');
  };

  this.enableCancel = function() {
    this.formCancelButton.removeClass('disabled');
  };

  this.checkSubmittable = function() {
    if (this.outletField.val()) {
      this.enableCreate();
    } else {
      this.disableCreate();
    }
  };

  this.showForm = function() {
    this.formContainer.show();
  };

  this.hideForm = function() {
    this.formContainer.hide();
  };

  this.startSpinner = function() {
    this.spinner.show();
  };

  this.stopSpinner = function() {
    this.spinner.hide();
  };

  this.createSyndication = function() {
    var data = {
      title: this.syndicationTitleField.val(),
      outlet: parseInt(this.outletField.val()),
      post: parseInt(this.postID),
      status: "publish"
    };
    this.startSpinner();
    this.disableCreate();
    this.disableCancel();
    this.noticeBox.html("");
    this.noticeBox.removeClass('error');

    jQuery.ajax({
      type: "post",
      url: "/wp-json/wp/v2/" + this.syndicationType,
      dataType: "json",
      contentType: "application/json",
      data: JSON.stringify(data),
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', this.wpAPINonce);
      },
      context: this
    })
      .done(function(response, textStatus) {
        var syndicationList = select('.pods-form-ui-field-name-pods-meta-'
                                     + this.medium
                                     + '-syndications ul.select2-choices');
        var fakeItem = select("<li class=\"select2-search-choice\"><div>"
                              + data.title + "</div></li>");
        syndicationList.append(fakeItem);
        /* Add new syndication to post form so it doesn't try to remove
           the post from the syndication we just created if someone
           submits the current post form */
        var syndications = this.syndicationsInput.val();
        if (syndications) {
          syndications += "," + response.id;
        } else {
          syndications = response.id;
        }
        this.syndicationsInput.val(syndications);
        this.hideForm();
        // clear title so it's not confusing if creating another syndication.
        this.syndicationTitleField.val('');
      })
      .fail(function(response, textStatus) {
        console.log(textStatus, response);
        this.noticeBox.html("An error occurred. It might be resolved by "
                            + "reloading the page and trying again.");
        this.noticeBox.addClass('error');
      })
      .always(function() {
        this.stopSpinner();
        this.enableCreate();
        this.enableCancel();
      });
  };

  /**********************
  Register event handlers and jiggle the DOM
  ***********************/

  this.syndicationTitleField.attr('disabled', 'true');

  /* Rename the form fields we don't actually want involved in the Post form */
  select('#' + this.containerId + ' input', 2).each(function(i, el) {
    var el = jQuery(el);
    el.prop('name', this.fieldName + '_' + el.prop('name'));
  });

  this.checkSubmittable();
  this.outletField.change(jQuery.proxy(this.updateSyndicationTitle, this));
  this.outletField.change(jQuery.proxy(this.checkSubmittable, this));
  this.newButton.on('click', this, function(e) {
    e.data.showForm();
  });

  this.formCancelButton.on('click', this,function(e) {
    if (!e.data.formCancelButton.hasClass('disabled'))
      e.data.hideForm();
  });

  this.syndicationsInput = select('input[name="pods_meta_'+ this.fieldName +'" ]');
  this.syndicationsInput.after(this.newButton);

  this.newButton.after(this.formContainer);

  this.createButton.on('click', this, function(e) {
    if (!e.data.createButton.hasClass('disabled')) {
      e.data.createSyndication(e.data);
    }
  });

}
