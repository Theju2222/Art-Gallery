@extends('voyager::master')
@section('css')
 <style>
   .order-top{
    
    flex-direction: column;
    margin-top : 2rem ;
    margin-bottom: 6rem;
}
.order-text>h2{

 margin: 2rem 0
    
}
.order-text>div>p{

    font-style: normal;
font-weight: 400;
font-size: 14px;
line-height: 25px
    
}
.myorder-btn{
    margin:1rem  0  ;
    padding:1rem 4rem ;
    border:none;
    background-color: #088FD8;
    color:white;

}
.bottom-box{
    max-width: 1000px;
   width: 100%;
    background: #F8F8F8;
border: 2px solid  #088FD8 !important;
display: flex;
flex-wrap: wrap;
z-index: 1111;
margin: auto;

}
.bottom-box>div{
    border: 1px solid #DADADA;
    flex:1 1 300px;
 height: 200px;
 padding-top: 2rem ;
 text-align: center;
}

.itemlist-container{
    display: flex;
    grid-gap:20px;
    flex-wrap: wrap;
    justify-content: center;
}
.item-list-box{
    display: flex;
    align-items: center;
    grid-gap:20px;
    box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.25);
    padding:0.5rem 1rem  ;
   width: 300px;
}
 
 </style>
@stop
@section('content')
<div class="order-top container center-div " >
    
    <div class='bottom-box section-margin'>
        
        <div >
            <strong>#Enquery_id</strong>
            <p>{{$enquery->id}}</p>
           
        </div>
        <div>
        <strong>Move Type</strong>
           
            <p>{{$enquery->from_bhk}}</p>
        </div>
        <div>
        <strong>User Information</strong>
        <p>{{$enquery->user->name}}</p>
        <p>{{$enquery->user->email}}</p>
        <p>{{$enquery->user->mobile}}</p>
        </div>
        <div>
        <strong>From</strong>
           <p>{{$enquery->from_location}}</p>
           <p> Floor: <span>{{$enquery->from_floor}}</span></p>
          
           @if($enquery->from_lift == 1)
           <p> Service Lift: <span>YES</span></p>
          
           @else
           
           <p> Service Lift: <span>NO</span></p>
           @endif
        </div>
        <div>
        <strong>To</strong>
          <p>{{$enquery->to_location}}</p>
          <p> Floor: <span>{{$enquery->to_floor}}</span></p>
          
          @if($enquery->to_lift == 1)
          <p> Service Lift: <span>YES</span></p>
         
          @else
          
          <p> Service Lift: <span>NO</span></p>
          @endif
        </div>
        <div>
        <strong>Date & TimeSlot</strong>
        <p>{{$enquery->date}}</p>
          
        </div>
    </div>
    <div style="margin-top:50px;">
        <p style="font-weight:600; font-size:16px; text-align:center">Your Selected Items</p>
    </div>
    <div class='itemlist-container'>

        @foreach($products as $key => $product)
        <div class='item-list-box '>
            <!-- <div>
                <img src="#" />
            </div> -->

     
            <div>
                <h4 style="margin:3px">{{$key}}</h4>

                @foreach($product as $attribute)
                <p style="font-size:16px">

                   <span>{{$attribute['attribute_name']}}:</span> 
                   <span>{{$attribute['attribute_value']}}</span> 
                   
                </p>
                @endforeach
            </div>
        </div>
        @endforeach
        
   
    </div>
</div>
@stop