<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use JavaScript;
use Carbon\Carbon;
use App\Vinnies\Access;
use App\Vinnies\Helper;
use Illuminate\Http\Request;
use App\Rules\StrongPassword;
use App\Rules\ValidEmailDomain;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Password;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.users');

        $per_page = config('vinnies.pagination.users');
        $users    = User::paginate($per_page);

        return view('users.list')->with(compact('users', 'per_page'));
    }

    public function showCreateForm()
    {
        $this->authorize('create.users');

        return view('users.create');
    }

    public function create(Request $request)
    {
        $this->authorize('create.users');

        $roles  = Access::getRoles()->toArray();
        $states = Helper::getStates();

        // Validate this
        $this->validate($request, [
            'first_name'     => 'required',
            'last_name'      => 'required',
            'branch_display' => 'required',
            'has_accepted_conditions' => 'required',
            'conditions_accepted_at'  => 'sometimes|nullable|date_format:' . config('vinnies.date_only_format'),
            'email'          => [
                'required',
                'email',
                'unique:users',
                new ValidEmailDomain,
            ],
            'state' => [
                'required',
                Rule::in(array_keys($states))
            ],
            'role' => [
                'required',
                Rule::in(array_keys($roles))
            ]
        ]);

        $data  = $request->only(['first_name', 'last_name', 'email', 'state', 'branch_display','has_accepted_conditions','conditions_accepted_at']);

        $data['password']               = bcrypt(str_random(12));
        $data['is_new']                 = true;
        $data['conditions_accepted_at'] = ($request->get('conditions_accepted_at')) ? Carbon::createFromFormat('d/m/Y H:i:s', $request->get('conditions_accepted_at') . '00:00:00') : null;

        $msg  = 'New user has been successfully created';
        $user = User::create($data);

        $user->syncRoles([$roles[$request->get('role')]]);
        $user->update([
            'google2fa_secret' => (new Google2FA)->generateSecretKey(),
        ]);

        Password::broker()->sendResetLink(['email' => $user->email]);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(User $user)
    {
        $this->authorize('update.users');

        $selectedRole = array_search($user->roles()->pluck('name')->first(), Access::getRoles()->toArray());

        JavaScript::put([
            'meta_url'          => route('users.meta', $user->id),
            'documentable_type'  => 'User',
            'documentable_id'    => $user->id,
        ]);
        
        return view('users.edit')->with(compact('user', 'selectedRole'));
    }

    public function edit(Request $request, User $user)
    {
        $this->authorize('update.users');

        $states = Helper::getStates();
        $roles  = Access::getRoles()->toArray();
        $rules  = [
            'first_name'     => 'required',
            'last_name'      => 'required',
            'branch_display' => 'required',
            'has_accepted_conditions' => 'required',
            'conditions_accepted_at'  => 'sometimes|nullable|date_format:' . config('vinnies.date_only_format'),
            'email'          => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
                new ValidEmailDomain,
            ],
            'state' => [
                'required',
                Rule::in(array_keys($states))
            ],
            'role' => [
                'required',
                Rule::in(array_keys($roles))
            ]
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'min:8',
                new StrongPassword,
            ];
        }

        $request->validate($rules);

        $roles = Access::getRoles()->toArray();
        $data  = $request->only(['first_name', 'last_name', 'email', 'state', 'branch_display','has_accepted_conditions','conditions_accepted_at']);

        if ($request->get('password') == '') {
            $request->except(['password']); 
        } else {
            $data['password'] = bcrypt($request->get('password'));
        }

        $data['conditions_accepted_at'] = ($request->get('conditions_accepted_at')) ? Carbon::createFromFormat('d/m/Y H:i:s', $request->get('conditions_accepted_at') . '00:00:00') : null;

        $user->update($data);
        $user->syncRoles([$roles[$request->get('role')]]);

        $msg = 'User has been successfully edited';

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.users');

        $users = User::whereNotNull('is_active');
        $users = $this->sortModelFromRequest($users, $request);

        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'active':
                        $users->where('is_active', 1);
                        break;

                    case 'not-active':
                        $users->where('is_active', 0);
                        break;
                }
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $users->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        }

        $users = $users->paginate(config('vinnies.pagination.users'));
        $data  = $this->getDatatableBaseData($users, $request);

        foreach ($users as $user) {
            $data['data'][] = [
                'id'         => $user->id,
                'state'      => $user->getState(),
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'role'       => $user->roles()->pluck('name')->first(),
                'mfa'        => $user->hasGoogle2FAEnabled() ? 'Activated' : '-' ,
                'last_login' => $user->getLastLoginDt() ? $user->getLastLoginDt()->format(config('vinnies.date_format')) : 'Never',
                'email'      => $user->email,
                'DT_RowId'   => 'row_' . $user->id
            ];
        }

        return $data;
    }

    public function multiDeactivate(Request $request)
    {
        $this->authorize('update.users');

        $users = $request->get('users');

        if (empty($users)) {
            return response()->json([
                'msg' => 'Invalid users supplied.'
            ], 400);
        }

        $users = Helper::getRowIds($users);

        User::whereIn('id', $users)->update(['is_active' => 0]);

        return response()->json([
            'msg' => 'Selected users have been successfully deactivated.'
        ]);
    }

    public function deactivate(Request $request)
    {
        // $this->authorize('update.users');

        $user = User::find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }

        $user->is_active = 0;
        //delete password, actually just set the random password since the password is not nullable
        $user->password = bcrypt(str_random(12));
        // $user->delete();
        // $user->deactivate();
        $user->save();

        return response()->json([
            'msg' => 'Selected user have been successfully deactivated.'
        ]);
    }

    public function reactivate(Request $request)
    {
        $this->authorize('update.users');

        $user = User::find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }

        $user->is_active = 1;
        $user->google2fa_enabled_at = NULL;
        $user->has_accepted_terms = 0;
        $user->has_accepted_conditions = 0;
        $user->save();
        
        $request['email'] = $user->email;
        Password::broker()->sendResetLink($request->only('email'));

        return response()->json([
            'msg' => 'Selected user have been successfully reactivated.'
        ]);
    }

    public function signtos(Request $request)
    {
        $user = User::find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }

        $user->has_accepted_terms = "0";
        $user->has_accepted_conditions = "0";
        $user->conditions_accepted_at = null;
        $user->save();

        return response()->json([
            'msg' => 'Selected user have been successfully signed the Term of Use'
        ]);
    }
}
