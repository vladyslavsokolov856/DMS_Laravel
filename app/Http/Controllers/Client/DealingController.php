<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Dealing\StoreRequest;
use App\Http\Requests\Client\Dealing\UpdateRequest;
use App\Infrastructures\Models\Eloquent\DomainDealing;
use App\Infrastructures\Models\Interval;
use App\Services\Application\DealingStoreService;
use App\Services\Application\DealingUpdateService;

use Exception;

use Illuminate\Support\Facades\Auth;

class DealingController extends Controller
{
    protected const INDEX_ROUTE = 'dealing.index';

    public function __construct()
    {
        parent::__construct();

        $this->middleware('can:owner,domainDealing')->only(['edit']);

        $this->middleware(function ($request, $next) {
            $domainList = Auth::user()->domains->pluck('name', 'id')->toArray();
            $clientList = Auth::user()->clients->pluck('name', 'id')->toArray();
            $intervalCategories = Interval::getIntervalList();

            view()->share([
                'domainList' => $domainList,
                'clientList' => $clientList,
                'intervalCategories' => $intervalCategories,
            ]);

            return $next($request);
        });
    }

    public function index()
    {
        $domains = Auth::user()->domains;

        $domains->load([
            'domainDealings',
            'domainDealings.client',
        ]);

        return view('client.dealing.index', compact('domains'));
    }

    public function new()
    {
        return view('client.dealing.new');
    }

    public function edit(DomainDealing $domainDealing)
    {
        // $this->authorize('owner', $domainDealing);

        return view('client.dealing.edit', compact('domainDealing'));
    }

    public function update(
        UpdateRequest $request,
        DomainDealing $domainDealing,
        DealingUpdateService $dealingUpdateService
    ) {
        $domainDealingRequest = $request->makeDto();
        try {
            $dealingUpdateService->handle($domainDealingRequest, $domainDealing);
        } catch (Exception $e) {
            return $this->redirectWithFailingMessageByRoute(self::INDEX_ROUTE, 'Failing Update');
        }

        return $this->redirectWithGreetingMessageByRoute(self::INDEX_ROUTE, 'Update Success!!');
    }

    public function store(
        StoreRequest $request,
        DealingStoreService $dealingStoreService
    ) {
        $domainDealingRequest = $request->makeDto();

        try {
            $dealingStoreService->handle($domainDealingRequest);
        } catch (Exception $e) {
            return $this->redirectWithFailingMessageByRoute(self::INDEX_ROUTE, 'Failing Create');
        }

        return $this->redirectWithGreetingMessageByRoute(self::INDEX_ROUTE, 'Create Success!!');
    }

    public function detail(DomainDealing $domainDealing)
    {
        $this->authorize('owner', $domainDealing);

        $domainDealing->load(['domain','client']);

        return view('client.dealing.detail', compact('domainDealing'));
    }
}
