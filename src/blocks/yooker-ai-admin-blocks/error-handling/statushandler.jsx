const StatusCheck = ({ status, message }) => {
  let isSuccess = false;
  let errorText = "";

  // Handle ranges
  if (status >= 200 && status < 300) {
    isSuccess = true;
  } else if (status >= 400 && status < 500) {
    switch (status) {
      case 401:
        errorText = "Unauthorized: " + message;
        break;
      case 403:
        errorText = "Forbidden: " + message;
        break;
      case 404:
        errorText = "Not Found: " + message;
        break;
      default:
        errorText = `Client Error (${status}): ${message}`;
    }
  } else if (status >= 500 && status < 600) {
    errorText = `Server Error (${status}): ${message}`;
  } else {
    errorText = `Unexpected status code (${status}): ${message}`;
  }

  if (!isSuccess) {
    console.log(errorText, "Status code:", status);
  }

  return isSuccess;
};

export default StatusCheck;
