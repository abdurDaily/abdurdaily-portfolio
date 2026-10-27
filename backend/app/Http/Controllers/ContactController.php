<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $thanks = setting('contact_section.success', 'Thanks! Your message is in.');

        // Honeypot filled in: a bot. Answer as if it worked, store nothing.
        if (filled($request->input('website'))) {
            return $this->respond($request, $thanks);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{6,20}$/'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        // Saved first, so an inquiry is never lost even if the email notification fails.
        $message = ContactMessage::create($data + ['ip' => $request->ip()]);

        try {
            Mail::raw(
                "Name: {$message->name}\nEmail: {$message->email}\nPhone: " . ($message->phone ?: '—') . "\n\n{$message->message}",
                fn ($mail) => $mail->to(setting('contact.email'))
                    ->replyTo($message->email, $message->name)
                    ->subject("New project inquiry — {$message->name}")
            );
        } catch (Throwable $e) {
            report($e);
        }

        return $this->respond($request, $thanks);
    }

    private function respond(Request $request, string $thanks)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $thanks])
            : redirect()->to(route('home') . '#contact')->with('contact_status', $thanks);
    }
}
