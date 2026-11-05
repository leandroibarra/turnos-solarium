@php
    $oWhatsAppBranch = request()->attributes->get('oBranch');

    if (!$oWhatsAppBranch) {
        $oWhatsAppBranch = \App\Models\Branch::where('enable', 1)->orderBy('name')->first();
    }

    $sWhatsAppPhone = $oWhatsAppBranch
        ? preg_replace('/\D+/', '', $oWhatsAppBranch->phone)
        : null;

    $aJoinChatSettings = [
        'telephone' => $sWhatsAppPhone,
        'mobile_only' => false,
        'button_delay' => 3,
        'whatsapp_web' => true,
        'message_views' => 1,
        'message_delay' => 10,
        'message_badge' => false,
        'message_send' => __('Hello, can I ask you a question?'),
        'message_hash' => '',
    ];
@endphp

@if ($sWhatsAppPhone)
<div class="joinchat joinchat--right" data-settings='@json($aJoinChatSettings)'>
    <div class="joinchat__button">
        <div class="joinchat__button__open"></div>
        <div class="joinchat__button__sendtext">{{ __('Open chat') }}</div>
    </div>
    <svg height="0" width="0">
        <defs>
            <clipPath id="joinchat__message__peak">
                <path d="M17 25V0C17 12.877 6.082 14.9 1.031 15.91c-1.559.31-1.179 2.272.004 2.272C9.609 18.182 17 18.088 17 25z"/>
            </clipPath>
        </defs>
    </svg>
</div>

<script src="{{ asset('plugins/joinchat/joinchat.min.js') }}" type="text/javascript"></script>
@endif
