@props(['status'])
@php($enum = $status instanceof \App\Enums\AlertStatus ? $status : \App\Enums\AlertStatus::from($status))
<span class="badge badge-{{ $enum->tone() }}">{{ $enum->label() }}</span>
