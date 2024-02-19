<style>
@page {
    margin: 250px 50px 50px;
    text-align: center;
}

.page-break {
    page-break-after: always;
}
</style>

<?php $i = 0; ?>
@foreach ($donations as $donation)
    @if ($donation->amount < $donation->sponsorship_value)
        <p>Presented to:</p>
        <p><strong>{{ $donation->name_on_certificate }}</strong></p>
        <p>In {{ date('Y') }}</p>
        <p style="font-size:24px;">For assisting the St Vincent de Paul Society’s</p>
        <p style="font-size:24px;">Assist a Student Program</p>

        @if ( (sizeof($donations)-1) != $i )
            <div class="page-break"></div>
        @endif
    @else
        <div style="font-size:22px">
            <p>Presented to:</p>
            <p><strong><em>{{ $donation->name_on_certificate }}</em></strong></p>

            <p>in {{ date('Y') }}</p>

            <p>
                for supporting education in
                <?php
                $countries = array();

                foreach ($donation->sponsorships as $sponsorship) {
                    $countries[$sponsorship->student->country] = $sponsorship->student->country;
                }

                sort($countries);

                if (sizeof($countries) > 1) {
                    $j = 1;

                    foreach ($countries as $country) {
                        if ($j == 1) {
                            ?><b>{{$country}}</b><?php
                        } elseif ($j == sizeof($countries)) {
                            ?> and <b>{{$country}}</b><?php
                        } else {
                            ?>, <b>{{$country}}</b><?php
                        }
                        $j++;
                    }
                } else {
                    ?><b>{{$sponsorship->student->country}}</b><?php
                }
                ?>
                <br><br />
                through the <strong>Assist A Student Program</strong>
            </p>
        </div>

        @if ( (sizeof($donations)-1) != $i )
            <div class="page-break"></div>
        @endif
    @endif
    <?php $i++; ?>
@endforeach
