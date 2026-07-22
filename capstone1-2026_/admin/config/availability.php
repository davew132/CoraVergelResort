<?php
// config/availability.php
// Shared logic for room inventory + double-booking prevention.
// require this AFTER config/conn.php in any page that books or manages rooms.

/**
 * Get total_units and price for a room type. Returns null if room not found.
 */
function getRoomInfo($conn, $room_name) {
    $stmt = $conn->prepare("SELECT room_id, room_name, price, total_units FROM rooms WHERE room_name = ?");
    $stmt->bind_param("s", $room_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/**
 * Get all rooms as an associative array keyed by room_name.
 * Useful for merging DB price/units into a page's static room display data.
 */
function getAllRooms($conn) {
    $rooms = [];
    $result = $conn->query("SELECT room_id, room_name, price, total_units FROM rooms ORDER BY room_name");
    while ($row = $result->fetch_assoc()) {
        $rooms[$row['room_name']] = $row;
    }
    return $rooms;
}

/**
 * Count how many rooms of $room_name are already booked (pending or confirmed)
 * for any date that overlaps the requested [$check_in, $check_out) range.
 *
 * Two date ranges overlap if: existing.check_in < new.check_out AND existing.check_out > new.check_in
 *
 * @param mysqli $conn
 * @param string $room_name
 * @param string $check_in   Y-m-d
 * @param string $check_out  Y-m-d
 * @param int|null $exclude_booking_id  Exclude a specific booking (useful when editing an existing booking)
 * @return int  number of overlapping bookings currently holding a unit
 */
function countOverlappingBookings($conn, $room_name, $check_in, $check_out, $exclude_booking_id = null) {
    $sql = "SELECT COUNT(*) c FROM bookings
            WHERE room_type = ?
              AND status IN ('pending','confirmed')
              AND check_in < ?
              AND check_out > ?";
    $params  = [$room_name, $check_out, $check_in];
    $types   = "sss";

    if ($exclude_booking_id !== null) {
        $sql .= " AND booking_id != ?";
        $params[] = $exclude_booking_id;
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['c'];
    $stmt->close();
    return (int) $count;
}

/**
 * How many units of this room type are still free for the given date range.
 * Returns null if the room type doesn't exist in the rooms table.
 */
function getAvailableUnits($conn, $room_name, $check_in, $check_out, $exclude_booking_id = null) {
    $room = getRoomInfo($conn, $room_name);
    if (!$room) return null;

    $booked = countOverlappingBookings($conn, $room_name, $check_in, $check_out, $exclude_booking_id);
    return max(0, $room['total_units'] - $booked);
}

/**
 * Convenience check used right before inserting a booking.
 * Returns true if there's at least 1 unit free, false if fully booked.
 */
function isRoomAvailable($conn, $room_name, $check_in, $check_out, $exclude_booking_id = null) {
    $available = getAvailableUnits($conn, $room_name, $check_in, $check_out, $exclude_booking_id);
    // If the room type isn't in the rooms table at all, fail safe (block booking)
    // rather than silently allowing unlimited bookings.
    if ($available === null) return false;
    return $available > 0;
}