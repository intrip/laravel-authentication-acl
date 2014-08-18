"use strict";

var appender = function (spec) {
  var that = {};
  // contains the ordering filters
  var orderings = [];
  // the separator used for the hidden ordering fields
  var separator = ",";

  var ordering_select = $("#" + spec.ordering_select);
  var order_by_select = $("#" + spec.order_by_select);
  var ordering_fied = $("#" + spec.ordering_field);
  var order_by_field = $("#" + spec.order_by_field);
  var append_sorting_field = $("#" + spec.append_sorting);

  that.initialize = function () {
    var i;
    var ordering_tokens = ordering_fied.val().split(separator);
    var order_by_tokens = order_by_field.val().split(separator);
    // validation
    if (order_by_tokens.length != order_by_tokens.length) return;

    // create orderings array
    for (i = 0; i < order_by_tokens.length; i++) {
      if (order_by_tokens[i].length == 0 || ordering_tokens[i].length == 0) continue;

      orderings.push({
        "order_by": order_by_tokens[i],
        "ordering": ordering_tokens[i]
      });
    }

    // render fields
    for (i = 0; i < orderings.length; i++) {
      that.appendPlaceholder(i);
    }
  }

  that.addOrdering = function () {
    that.appendPlaceholder(that.appendOrdering());
  };

  that.appendPlaceholder = function (index) {
    var order_by_placeholder = '<div class="col-md-12">' +
        '<input type="text" disabled="disabled" class="form-control margin-top-10" value="' + orderings[index].order_by + '">' +
        '</div>';
    var field_placeholder = '<div class="col-md-12" field="' + orderings[index].ordering + '">' +
        '<input type="text" disabled="disabled" class="form-control margin-top-10" value="' + orderings[index].ordering + '" >' +
        '</div>';
    var delete_ordering = '<div class="col-md-12 margin-top-10">' +
        '<a onclick="myAppender.removeOrdering(' + index + ');" class="btn btn-default pull-right ' + spec.remove_ordering_button + '"><i class="fa fa-minus"></i> Remove</a>' +
        '</div>';

    append_sorting_field.append('<div class="ordering-container" id="ordering_' + index + '" >' + order_by_placeholder + field_placeholder + delete_ordering + '</div>');
  }

  that.removeOrdering = function (number) {
    var ordering_fields = document.getElementById('ordering_' + number);
    ordering_fields.parentNode.removeChild(ordering_fields);

    orderings.splice(number, 1);
  }

  that.appendOrdering = function () {
    var ordering_value = ordering_select.val();
    var order_by_value = order_by_select.val();

    orderings.push({
      "ordering": ordering_value,
      "order_by": order_by_value
    });

    return (orderings.length - 1);
  }

  that.fillOrderingInput = function () {
    var i;
    var ordering_str;
    var order_by_str;

    for (i = 0; i < orderings.length; i++) {
      if (i == 0) {
        order_by_str = orderings[i].order_by;
        ordering_str = orderings[i].ordering;
      }
      else {
        order_by_str += separator + orderings[i].order_by;
        ordering_str += separator + orderings[i].ordering;
      }
    }

    $("#" + spec.order_by_field).val(order_by_str);
    $("#" + spec.ordering_field).val(ordering_str);
  }

  that.validate = function () {
    var order_by_value = order_by_select.val();

    that.clearErrorMessage();

    if (order_by_value == "") {
      that.setErrorMessage(spec.order_by_select, spec.order_by_error_messsage);
      return false;
    }

    return true;
  }

  that.clearErrorMessage = function () {
    $(".form-validable").removeClass('form-error');
    $(".form-error-message").addClass('hidden');
  }

  that.setErrorMessage = function (field_id, value) {
    $("#" + field_id).addClass('form-error');
    $(".form-error-message").removeClass('hidden');
  }

  that.getOrderings = function(){
    return orderings;
  };

  return that;
};

// appender constructor data
var spec = {
  order_by_field: "order-by",
  ordering_field: "ordering",
  order_by_select: "order-by-select",
  ordering_select: "ordering-select",
  append_sorting: "append-sorting",
  add_ordering_button: "add-ordering-filter",
  remove_ordering_button: "remove-ordering-button",
  search_submit_button: "search-submit",
  search_reset_button: "search-reset",
};

var myAppender = appender(spec);

$(document).ready(function () {
  myAppender.initialize();

  $("#" + spec.add_ordering_button).click(function () {
    if (!myAppender.validate()) {
      return;
    }
    myAppender.addOrdering();
  });

  $("#" + spec.search_submit_button).click(function (e) {
    myAppender.fillOrderingInput();
  });
});

