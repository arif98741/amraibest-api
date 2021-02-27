@extends('layouts.admin')

@section('content')
                    <input type="hidden" id="headerdata" value="Vendor Comission">
                    <div class="content-area">
                        <div class="mr-breadcrumb">
                            <div class="row">
                                <div class="col-lg-12">
                                		<h4 class="heading">Vendor Comission</h4>
										<ul class="links">
											<li>
												<a href="/admin">Dashboard</a>
											</li>
										</ul>
								</div>
							</div>
						</div>
						<div class="product-area">
							<div class="row">
								<div class="col-lg-12">

									<div class="mr-table allproduct">
										@include('includes.admin.form-success')
										<div class="table-responsiv">
												<table id="geniustable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
													<thead>
														<tr>
		                                                  <th>Store Name</th>
		                                                  <th>Total Comission</th>
		                                                  <th>Status</th>
		                                                  <th>Option</th>
														</tr>
													</thead>
                                                    <tbody>
                                                      @foreach($vendor as $comission)
                                                        <tr>
                                                            <td>{{$comission->shop_name}}</td>
                                                            <td>৳{{$comission->comission}} </td>
                                                            <td>
                                                                @if($comission->status == 0)
                                                                    <b> Unpaid </b>
                                                                @else
                                                                    <b> Paid <b>
                                                                @endif
                                                            </td>
                                                            <td>
                                                            @if($comission->status == 0)
                                                                <button data-id="{{$comission->vendor_id}}" data-toggle="modal" data-target="#exampleModal{{$comission->vendor_id}}" class="btn btn-success">
                                                                    <b> Make Paid</b>
                                                                    </button>
                                                                <a href="/admin/vendor-comission/make-request/{{$comission->vendor_id}}/{{$comission->comission}}" class="btn btn-success">
                                                                    <b> Make Request</b>
                                                                    </a>
                                                                @else

                                                                @endif

                                                            </td>
                                                        </tr>

                                                    <!-- Modal -->
                                                        <div class="modal fade" id="exampleModal{{$comission->vendor_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                          <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Make Paid</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                  <span aria-hidden="true">&times;</span>
                                                                </button>
                                                              </div>
                                                              <div class="modal-body">
                                                                <form action="/admin/vendor-comission/make-paid" method="POST">
                                                                {{ csrf_field() }}
                                                                        <input type="hidden" name="id"  value="{{$comission->vendor_id}}">
                                                                    <div class="form-grroup">
                                                                        <input class="form-control" type="number" name="paid">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input class="form-control" type="submit" value="Make Paid">
                                                                    </div>
                                                                </form>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      @endforeach
                                                    </tbody>
												</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>



@endsection

