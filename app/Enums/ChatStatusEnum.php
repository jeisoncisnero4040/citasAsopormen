<?php

namespace App\Enums;

enum ChatStatusEnum: string
{
    case NEW = 'new'; 

    case VERIFY_IDENTITY = 'verify_identity'; 
    case IDENTIFIED = 'identified'; 
    
    case CONSULTING_APPOINTMENTS = 'consulting_appointments';
    case CANCELING_APPOINTMENTS = 'canceling_appointments';
    case CANCEL_APPOINTMENT = 'cancel_appointment';
    case CONFIRMING_APPOINTMENT = 'confirming_appointment';

    case WAITING_REASON = 'waiting_reason'; 

    case WAITING_APPOINTMENT_SELECTION = 'waiting_appointment_selection';

    case READY_TO_CANCEL = 'ready_to_cancel';
    case READY_TO_CONFIRM = 'ready_to_confirm';

    case COMPLETED = 'completed'; 

    
}