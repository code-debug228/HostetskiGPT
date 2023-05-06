
function hostetskigptInit() {
	$(document).ready(function(){
        $("body").prepend(`<style>
            .thread-gpt {
               margin: 30px;
               padding: 10px 20px;
               text-align: center;
               border: 1px solid #ebe534;
               background-color: rgba(221, 224, 36, 0.2);
           }
           .gpt-loader {
               width: 18px;
               height: 18px;
               margin-right: 4px;
           }
        </style>
        `);

		$(".thread-options ul.dropdown-menu").append("<li><a class=\"chatgpt-get\" href=\"#\" target=\"_blank\" role=\"button\">Generate answer(ChatGPT)</li>");
        $(".conv-customer-block").append("<div class=\"chatgpt-answers\"></div>");

        const conversation_id = $("body").attr("data-conversation_id");
        $.ajax({
            url: '/hostetskigpt/answers?conversation='+conversation_id,
            dataType: 'json',
            success: function(response, status) {
                response.answers.forEach(function(item, index, array) {
                    $("#thread-"+item.thread).prepend("<div class=\"thread-gpt\"><strong>ChatGPT:</strong><br />"+item.answer+"</div>");
                });
            }
        });

        $(".chatgpt-get").on('click', function(e) {
            e.preventDefault();
            const text = $(e.target).closest(".thread").children(".thread-message").children(".thread-body").children(".thread-content").get(0).innerHTML;
            const query = encodeURIComponent(text.replace("<.*?>", ""));
            const thread_id = $(e.target).closest(".thread").attr("data-thread_id");

            $('#thread-' + thread_id + ' .thread-info').prepend("<img class=\"gpt-loader\" src=\"/modules/hostetskigpt/img/loading.gif\" alt=\"Test\">");

            fsAjax("query=" + query + "&thread_id=" + thread_id, '/hostetskigpt/generate', function (response) {
                $(e.target).closest(".thread").prepend("<div class=\"thread-gpt\"><strong>ChatGPT:</strong><br />"+response.answer+"</div>");
                $('#thread-' + thread_id + ' .gpt-loader').remove();
            }, true, function() {
                showFloatingAlert('error', Lang.get("messages.ajax_error"));
                $('#thread-' + thread_id + ' .gpt-loader').remove();
            });
        });
	});
}
