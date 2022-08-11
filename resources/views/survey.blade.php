<div>
    <p>Hi {{ $customer->name }},</p>

    <p>Thank you for using our application!</p>

    <p>Please take a moment to answer the following question:</p>

    <p>{{ $survey->question }}</p>

    @if ($survey->question_type == "likert")
    <ul style="list-style-type: none">
        @for ($i = 0; $i < 10; $i++)
            <li>
                <a href="{{ $survey->generateLink($customer) }}?answer={{ $i }}">
                    {{ $i }}
                </a>
            </li>
        @endfor
    </ul>
    @endif

    @if ($survey->question_type == "yes_no")
    <ul style="list-style-type: none">
        <li>
            <a href="{{ $survey->generateLink($customer) }}?answer=yes">
                yes
            </a>
        </li>
        <li>
            <a href="{{ $survey->generateLink($customer) }}?answer=no">
                no
            </a>
        </li>
    </ul>
    @endif

    <p>Thank you!</p>
</div>
