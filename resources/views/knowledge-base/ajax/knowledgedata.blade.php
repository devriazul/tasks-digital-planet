    @php
        $knowledgebase_count = 0;
    @endphp

    @foreach ($categories as $category)

    <div class="col-md-4">
        <div class="card border-0 b-shadow-4 mb-3 e-d-info" style="height: 200px;overflow: auto;">
            <div class="card-horizontal">
                <div class="card-body border-0">
                    <h4 class="card-title f-15 f-w-500 mb-3 d-flex justify-content-between">

                    <span> <i class="bi bi-house mr-2"></i>{{ $category->name }} ( {{ $count[$knowledgebase_count]['counts'] }} )  </span>

                    @if ($addknowledgebasePermission == 'all' || $addknowledgebasePermission == 'added')
                    <span>
                            <i class="icon-options-vertical icons" id="dropdownMenuLink-3" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item openRightModal" href="{{ route('knowledgebase.create', ['id' => $category->id]) }}">
                                    <i class="fa fa-plus mr-2"></i>
                                    @lang('app.create')
                                </a>
                            </div>
                    </span>
                    @endif
                </h4>
                    <ul>
                        @foreach($knowledgebases as $knowledgebase)
                            @if($knowledgebase->category_id == $category->id)
                                <li class="d-flex justify-content-between mb-2">
                                    <a href="{{ route('knowledgebase.show', $knowledgebase->id) }}" class="openRightModal text-darkest-grey" >{{  $knowledgebase->heading }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @php
        $knowledgebase_count++;
    @endphp

    @endforeach