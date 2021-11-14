import moment from "moment";
import 'moment/locale/zh-cn'

export function difForHumans(date,format='YYYYMMDD H:mm:ss') {
  moment.locale('zh-cn')
  return moment(date,format).fromNow()
}
