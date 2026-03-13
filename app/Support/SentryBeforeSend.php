<?php

namespace App\Support;

class SentryBeforeSend
{
    public function __invoke(\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event
    {
        $request = $event->getRequest();
        if (is_array($request) && !empty($request['url'])) {
            $request['url'] = strtok($request['url'], '?') ?: $request['url'];
            $request['headers'] = [];
            $request['data'] = null;
            $event->setRequest($request);
        }
        return $event;
    }
}
