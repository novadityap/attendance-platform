import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(localizedFormat);

if (typeof window !== 'undefined') {
  const userTZ = Intl.DateTimeFormat().resolvedOptions().timeZone;
  dayjs.tz.setDefault(userTZ);
}

export default dayjs;
