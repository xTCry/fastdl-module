<?php

namespace GameapModules\Fastdl\Http\Controllers;

use Gameap\Models\DedicatedServer;
use GameapModules\Fastdl\Http\Requests\FastdlDsRequest;
use GameapModules\Fastdl\Models\FastdlDs;
use GameapModules\Fastdl\Repository\FastdlDsRepository;
use GameapModules\Fastdl\Services\FastdlService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Gameap\Http\Controllers\AuthController;
use Illuminate\View\View;

class FastdlController extends AuthController
{
    /**
     * @var FastdlDsRepository
     */
    protected $fastdlDsRepository;

    /**
     * @var FastdlService
     */
    protected $fastdlService;

    /**
     * FastDlController constructor.
     * @param FastdlDsRepository $fastdlDsRepository
     * @param FastdlService $fastdlService
     */
    public function __construct(FastdlDsRepository $fastdlDsRepository, FastdlService $fastdlService)
    {
        parent::__construct();

        $this->fastdlDsRepository = $fastdlDsRepository;
        $this->fastdlService = $fastdlService;
    }

    /**
     * Display a listing of the resource.
     * @return Factory|View
     */
    public function index()
    {
        $fastdlDedicatedServers = $this->fastdlDsRepository->getList();
        return view('fastdl::list', compact('fastdlDedicatedServers'));
    }

    /**
     * Show the specified resource.
     * @param FastdlDs $fastdlDs
     * @return Factory|View|RedirectResponse
     */
    public function show(FastdlDs $fastdlDs)
    {
        if (!$fastdlDs->installed) {
            return redirect()->route('admin.fastdl.edit', route('id'));
        }

        return view('fastdl::show', compact('fastdlDs'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Factory|View
     */
    public function edit(int $id)
    {
        $dedicatedServer = DedicatedServer::findOrFail($id);
        $fastdlDs = $this->fastdlDsRepository->get($id);
        return view('fastdl::edit', compact('dedicatedServer', 'fastdlDs'));
    }

    /**
     * Save the specified resource in storage.
     * @param FastdlDsRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function save(FastdlDsRequest $request, int $id)
    {
        $attributes = $request->all();

        $dedicatedServer = DedicatedServer::findOrFail($id);
        $fastdlDs = $this->fastdlDsRepository->get($dedicatedServer->id);

        // Checkboxes
        $attributes['autoindex'] = isset($attributes['autoindex']);
        $attributes['autoinstall'] = isset($attributes['autoinstall']);

        if ($fastdlDs === null) {
            $fastdlDs = FastdlDs::create(array_merge(['ds_id' => $id, 'installed' => true], $attributes));

            if ($attributes['autoinstall']) {
                $gdaemonTaskId = $this->fastdlService->install($fastdlDs);
            }

        } else {
            $fastdlDs->update($attributes);
        }

        return redirect()->route('admin.fastdl')
            ->with('success', __('fastdl::fastdl.update_ds_settings_success_msg'));
    }
}
