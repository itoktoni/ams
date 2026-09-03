<script>
    (function() {
        var options = @json($chart->getOptions());
        options.chart = options.chart || {};
        options.chart.id = '{!! $chart->id() !!}';
        @if($chart->labels())
        options.labels = {!! json_encode($chart->labels(), true) !!};
        @endif

        var el = document.querySelector("#{!! $chart->id() !!}");
        if (!el) return;
        var chart = new ApexCharts(el, options);
        chart.render();
    })();
</script>
