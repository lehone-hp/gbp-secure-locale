    </div>{{-- .email-body --}}
    <div class="email-footer">
        <p>{!! __('emails.all_rights_reserved', ['year' => date('Y'), 'app_name' => config('app.name')]) !!}</p>
        @if(isset($automatedNote))
        <p>{{ $automatedNote }}</p>
        @endif
    </div>
</div>{{-- .email-wrapper --}}
</body>
</html>
