	

	$(function() {
        $('#dateTimeRange').daterangepicker({
            applyClass : 'btn-sm btn-success',
            cancelClass : 'btn-sm btn-default',
            alwaysShowCalendars: true,
            locale: {
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                fromLabel : 'From',
                toLabel : 'To',
                customRangeLabel : 'Custom Range',
                firstDay : 1
            },
            ranges : {
                //'最近1小时': [moment().subtract('hours',1), moment()],
                'Today': [moment().startOf('day'), moment()],
                'Yesterday': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf("month"),moment().endOf("month")],
                'Last Month': [moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")]
            },
            opens : 'right',    // 日期选择框的弹出位置
            separator : ' - ',
            showWeekNumbers : true,     // 是否显示第几周
            startDate : begindate,
            endDate : enddate,

            //timePicker: true,
            //timePickerIncrement : 10, // 时间的增量，单位为分钟
            //timePicker12Hour : false, // 是否使用12小时制来显示时间


            //maxDate : moment(),           // 最大时间
            format: 'YYYY-MM-DD'

        }, function(start, end, label) { // 格式化日期显示框
            $('#beginTime').val(start.format('YYYY-MM-DD'));
            $('#endTime').val(end.format('YYYY-MM-DD'));
        })
        .next().on('click', function(){
            $(this).prev().focus();
        });
    });

    /**
     * 清除时间
     */
    function begin_end_time_clear() {
        $('#dateTimeRange').val('');
        $('#beginTime').val('');
        $('#endTime').val('');
    }