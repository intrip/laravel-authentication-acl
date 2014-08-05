"use strict";

var appender = function (spec) {
  var that = {};

  that.addOrdering = function () {
    var ordering_select = $("#"+spec.ordering_select);
    var order_by_select = $("#"+spec.order_by_select);
    var ordering_fied = $("#"+spec.ordering_field);
    var order_by_field = $("#"+spec.order_by_field);

    // create new strings
    var new_ordering = that.appendField( ordering_fied, ordering_select );
    var new_order_by = that.appendField( order_by_field, order_by_select );

    //@todo add jquery validation to make column required

    // fill them
    ordering_fied.val(new_ordering);
    order_by_field.val(new_order_by);

    //@todo add the ordering filter visible and possibility to delete him
  };

  that.removeOrdering = function () {
  }

  that.resetOrdering = function () {

  }

  that.appendField = function(str, append, separator) {
    var str_value = str.val();
    var append_value = append.val();
    var separator = separator || '|';

    return str_value + ((str_value.length === 0) ? append_value : (separator + append_value));
  }

  return that;
};

var appender_data = {
  order_by_field: "order-by",
  ordering_field: "ordering",
  order_by_select: "order-by-select",
  ordering_select: "ordering-select",
  add_ordering_filter : "add-ordering-filter"
};

var myAppender = appender(appender_data);

$("#"+appender_data.add_ordering_filter).click(function(){
  myAppender.addOrdering();
});