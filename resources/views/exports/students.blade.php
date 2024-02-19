<table>
    <thead>
    <tr>
        <th bgcolor="#7030a0">{{ $name }}</th>
        <th bgcolor="#0070c0">Education Sector Primary</th>
        <th bgcolor="#0070c0">Education Sector Secondary</th>
        <th bgcolor="#0070c0">Education Sector Tertiary</th>
        <th bgcolor="#0070c0">Education Sector N/A</th>
        <th bgcolor="#0070c0">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($data as $state => $stats)
        <tr>
          <?php
          switch ($state) {
              case 'national':
                  $header = 'Website/National';
                  break;

              default:
                  $header = strtoupper($state);
                  break;
          }
          ?>
        </tr>
        <tr>
            <td bgcolor="#7030a0"><b>{{$header}}</b></td>
            <td bgcolor="#7030a0"></td>
            <td bgcolor="#7030a0"></td>
            <td bgcolor="#7030a0"></td>
            <td bgcolor="#7030a0"></td>
            <td bgcolor="#7030a0"></td>
        </tr>

        @if ($stats['data']->isEmpty())
        <tr>
            <td><b>No student allocated</b></td>
        </tr>
        @else
            <?php
            $total_primary   = 0;
            $total_secondary = 0;
            $total_tertiary  = 0;
            $total_na        = 0;
            $total_all       = 0;
            ?>
            @foreach ($stats['data'] as $country => $sector)
                <?php
                $edu_primary   = !empty($sector['Primary']) ? $sector['Primary']->count() : 0;
                $edu_secondary = !empty($sector['Secondary']) ? $sector['Secondary']->count() : 0;
                $edu_tertiary  = !empty($sector['Tertiary']) ? $sector['Tertiary']->count() : 0;
                $edu_na        = !empty($sector['N/A']) ? $sector['N/A']->count() : 0;
                $total_row     = $edu_primary + $edu_secondary + $edu_tertiary + $edu_na;

                $total_primary   += $edu_primary;
                $total_secondary += $edu_secondary;
                $total_tertiary  += $edu_tertiary;
                $total_na        += $edu_na;
                $total_all       += $total_row;
                ?>
                <tr>
                  <td>{{ $country }}</td>
                  <td>{{ $edu_primary }}</td>
                  <td>{{ $edu_secondary }}</td>
                  <td>{{ $edu_tertiary }}</td>
                  <td>{{ $edu_na }}</td>
                  <td><b rgb="#FFFFEB9C">{{ $total_row }}</b></td>
                </tr>

            @endforeach

            <tr>
              <td bgcolor="#c6e0b4"><b>Total All</b></td>
              <td bgcolor="#c6e0b4"><b>{{ $total_primary }}</b></td>
              <td bgcolor="#c6e0b4"><b>{{ $total_secondary }}</b></td>
              <td bgcolor="#c6e0b4"><b>{{ $total_tertiary }}</b></td>
              <td bgcolor="#c6e0b4"><b>{{ $total_na }}</b></td>
              <td bgcolor="#c6e0b4"><b>{{ $total_all }}</b></td>
            </tr>

        @endif

    @endforeach
    </tbody>
</table>
