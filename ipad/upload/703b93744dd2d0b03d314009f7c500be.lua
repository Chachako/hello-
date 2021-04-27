-----------------------------Station config-----------------------------
package.cpath = Directory.frameworks .. '/?.dylib;' .. package.cpath
local serialPath = require("serial") 

TestUnit = {}

TestUnit[1] = {
  iPad = {
    path = "/dev/cu.usbserial-alcdut1",
    --path = pathTable[1]
    config = {baudRate=115200, parity="N", dataBits=8, stopBits=1, flowControl=0,timeout = 6.0},
  }
}
 --TestUnit[1].iPad.path = pathTable[1]


-- TestUnit[2] = {
--   iPad = {
--     path = "/dev/cu.usbserial-UUT2",
--    -- path = pathTable[2]
--     config = {baudRate=115200, parity="N", dataBits=8, stopBits=1, flowControl=0,timeout = 6.0},
--   }
-- }
-- --TestUnit[2].iPad.path = pathTable[2]

-- TestUnit[3] = {
--   iPad = {
--     path = "/dev/cu.usbserial-UUT3",
--     config = {baudRate=115200, parity="N", dataBits=8, stopBits=1, flowControl=0,timeout = 6.0},
--   }
-- }
-- --TestUnit[3].iPad.path = pathTable[3]

-- TestUnit[4] = {
--   iPad = {
--     path = "/dev/cu.usbserial-UUT4",
--     config = {baudRate=115200, parity="N", dataBits=8, stopBits=1, flowControl=0,timeout = 6.0},
--   }
-- }
--TestUnit[4].iPad.path = pathTable[4]
local paths = serialPath.getPaths("/dev/cu.usbserial")

if paths then
  table.sort(paths)
  for pathNum = 1 , #(paths) do 
    TestUnit[pathNum].iPad.path =paths[pathNum]
  end
end




