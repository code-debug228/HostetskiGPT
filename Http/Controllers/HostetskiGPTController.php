<?php

namespace Modules\HostetskiGPT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Thread;

class HostetskiGPTController extends Controller
{

    public string $token = "sk-...";
    public string $startmessage = "Act like a support agent";

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('hostetskigpt::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hostetskigpt::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hostetskigpt::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('hostetskigpt::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    public function get(Request $request) {
        if (Auth::user() === null) return Response::json(["error" => "Unauthorized"], 401);
        $openaiClient = \Tectalic\OpenAi\Manager::build(new \GuzzleHttp\Client(), new \Tectalic\OpenAi\Authentication($this->token));

        $response = $openaiClient->chatCompletions()->create(
        new \Tectalic\OpenAi\Models\ChatCompletions\CreateRequest([
            'model'  => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->startmessage
                ],
                [
                    'role' => 'user',
                    'content' => $request->query('query')
                ]
            ],
            'max_tokens' => 1024
        ])
        )->toModel();

        $thread = Thread::find($request->query('thread_id'));
        $answers = json_decode($thread->chatgpt, true);
        if ($answers === null) {
            $answers = [];
        }
        array_push($answers, trim($response->choices[0]->message->content, "\n"));
        $thread->chatgpt = json_encode($answers, JSON_UNESCAPED_UNICODE);
        $thread->save();

        return Response::json([
            'query' => $request->query('query'),
            'answer' => $response->choices[0]->message->content
        ], 200);
    }

    public function answers(Request $request) {
        if (Auth::user() === null) return Response::json(["error" => "Unauthorized"], 401);
        $conversation = $request->query('conversation');
        $threads = Thread::where("conversation_id", $conversation)->get();
        $result = [];
        foreach ($threads as $thread) {
            if ($thread->chatgpt !== "{}") {
                $answers_text = json_decode($thread->chatgpt, true);
                if ($answers_text === null) continue;
                $answer_text = end($answers_text);
                $answer = ["thread" => $thread->id, "answer" => $answer_text];
                array_push($result, $answer);
            }
        }
        return Response::json(["answers" => $result], 200);
    }

}
