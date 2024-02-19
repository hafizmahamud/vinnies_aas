<div class="modal fade" id="modal-students" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Custom student allocation</h4>
            </div>
            <div class="modal-body">
                <p>Please select one or more students to be allocated specifically to this donation. <span class="text-danger">Please ensure you select only unallocated students.</span></p>
                <form class="js-modal-table-filter">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="is_allocated">Allocation Status</label>
                                <select name="is_allocated" id="is_allocated" class="form-control">
                                    <option value="all">All Students</option>
                                    <option value="1">Allocated Students</option>
                                    <option value="0" selected>Unallocated Students</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="assistance_year">Assistance Year</label>
                                <select name="assistance_year" id="assistance_year" class="form-control">
                                    <option value="all">All Years</option>
                                    @foreach (range(date('Y'), config('vinnies.start_year')) as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="has_age">Age provided</label>
                                <select name="has_age" id="has_age" class="form-control">
                                    <option value="all">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">N/A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="age_from" style="display:block;">Age from</label>
                                <input type="text" id="age_from" class="form-control" name="age_from" style="width:70px; display:inline-block;">
                                <span class="ml-1">to</span>
                                <input type="text" id="age_to" class="form-control ml-1" name="age_to" style="width:70px; display:inline-block;">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="all">All</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <input type="hidden" name="per_page" value="10">
                                <button type="submit" class="btn btn-primary btn-block" data-text-progress="Applying..." data-text-default="Apply Filter">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6">
                        <form class="form-inline js-table-search-form">
                            <div class="form-group has-btn">
                                <label class="sr-only" for="modal-os-conf-search">Search</label>
                                <input type="text" class="form-control js-table-search-input" id="modal-os-conf-search" placeholder="Search">
                                <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6 text-right">
                        @include('pagination.basic-alt')
                    </div>
                </div>

                {!! Form::open(['url' => route('donations.students', $donation), 'class' => 'js-modal-form']) !!}
                    <table class="table table-striped js-modal-table" data-url="{{ route('students.datatables') }}" data-page-length="{{ config('vinnies.pagination.students') }}" data-order-col="1" data-order-type="DESC">
                        <thead>
                            <tr>
                                <th data-orderable="false"><input type="checkbox" class="js-select-all"></th>
                                <th class="text-center" data-name="id">Student ID</th>
                                <th class="text-center" data-name="first_name">First Name</th>
                                <th class="text-center" data-name="last_name">Last Name</th>
                                <th class="text-center" data-name="is_allocated">Allocated</th>
                                <th class="text-center" data-name="assistance_year">Assistance Year</th>
                                <th class="text-center" data-name="class">Class</th>
                                <th class="text-center" data-name="country">Country</th>
                                <th class="text-center" data-name="age">Age</th>
                                <th class="text-center" data-name="gender">Gender</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-danger" data-error="student_ids"></p>
                        </div>
                    </div>
                    <input type="hidden" name="force" value="0">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Allocating..." data-text-default="Allocate selected students">Allocate selected students</button>
            </div>
        </div>
    </div>
</div>
