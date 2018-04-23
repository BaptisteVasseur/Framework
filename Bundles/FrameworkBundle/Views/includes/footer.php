</div>
    <?= App::getRessource("frameworkBundle:js:bootstrap.min.js") ?>

    <script>
        for (var i = 10; i >= 0; i--) {
            setTimeout(hello, i * 100, i);
            var hello = function(i){
                $('.item-' + i).addClass('animated zoomIn opacity1').removeClass('opacity0');
            }
        }

        $('.confirmRedirectionLinkSelector').click(function(e){
            e.preventDefault();
            if(confirm( $(this).attr('data-confirmTitle') )){
                document.location.href = $(this).attr('href');
            }
        });
//        $('button[data-toggle="modal"]').click(function(e){
//            e.preventDefault();
//           $( $(this).attr('data-target') ).modal('show');
//        });
//
//        $('a').each(function(index){
//            $(this).attr('data-href', $(this).attr('href') );
//            $(this).removeAttr('href');
//        });
//
//        $('a').click(function(e){
//            e.preventDefault();
//            $.get( $(this).attr('data-href'), {}, function(donnees){
//                $('body').html(donnees);
//            });
//        })

    </script>

    </body>
</html>
