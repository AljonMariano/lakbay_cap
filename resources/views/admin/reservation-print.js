function printReservation(reservationId) {
    console.log('Attempting to print reservation:', reservationId);
    window.open('/admin/reservations/' + reservationId + '/print', '_blank');
}